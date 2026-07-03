#!/usr/bin/env node
/**
 * Fix Vapi assistant: transcriber, voice, greeting.
 *
 * Usage:
 *   VAPI_API_KEY=<key> node fix-vapi.mjs
 *
 * Reads assistant ID from .vapi-state.json.
 * Updates:
 *   - Transcriber → AssemblyAI multilingual (understands Mandarin)
 *   - Voice → Azure Jenny Multilingual (speaks English + Mandarin)
 *   - First message → bilingual greeting with language selection
 */

import fs from 'fs';
import path from 'path';
import https from 'https';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const API_BASE = 'https://api.vapi.ai';
const STATE_PATH = path.resolve(__dirname, 'docs/support-agent/.vapi-state.json');
const PROMPT_PATH = path.resolve(__dirname, 'docs/support-agent/vapi-assistant-prompt.md');

function api(method, urlPath, body) {
  return new Promise((resolve, reject) => {
    const u = new URL(urlPath, API_BASE);
    const key = process.env.VAPI_API_KEY;
    if (!key) { reject(new Error('VAPI_API_KEY not set')); return; }
    const opts = { hostname: u.hostname, port: 443, path: u.pathname, method,
      headers: { Authorization: `Bearer ${key}`, 'Content-Type': 'application/json' } };
    const req = https.request(opts, res => {
      let data = '';
      res.on('data', c => data += c);
      res.on('end', () => {
        try { const p = JSON.parse(data);
          if (res.statusCode >= 200 && res.statusCode < 300) resolve(p);
          else reject(new Error(`HTTP ${res.statusCode}: ${data}`)); }
        catch { reject(new Error(`HTTP ${res.statusCode} (non-JSON): ${data}`)); }
      });
    });
    req.on('error', reject);
    if (body) req.write(JSON.stringify(body));
    req.end();
  });
}

async function main() {
  // Load state
  let state;
  try { state = JSON.parse(fs.readFileSync(STATE_PATH, 'utf-8')); }
  catch { console.error('State file not found. Run setup-vapi.mjs first.'); process.exit(1); }
  const { assistantId, toolId, escalationToolId } = state;
  if (!assistantId) { console.error('No assistantId in state.'); process.exit(1); }
  const toolIds = [toolId, escalationToolId].filter(Boolean);

  console.log(`Updating assistant ${assistantId}...\n`);

  const promptContent = fs.readFileSync(PROMPT_PATH, 'utf-8');

  // Build update payload
  const payload = {
    transcriber: {
      provider: 'assembly-ai',
      language: 'multi',
      speechModel: 'universal-streaming-multilingual',
    },
    voice: {
      provider: 'azure',
      voiceId: 'en-US-JennyMultilingualNeural',
    },
    model: {
      provider: 'openai',
      model: 'gpt-4.1-mini',
      temperature: 0.2,
      messages: [{ role: 'system', content: promptContent }],
      toolIds,
    },
    firstMessage: 'Hello! Thank you for calling SVICLOUD TV Box support. 你好！感謝致電小雲電視盒客服。Please say English or 中文 to choose your language. 請說 English 或 中文 選擇語言。',
    firstMessageMode: 'assistant-speaks-first',
  };

  console.log('Changes:');
  console.log('  Transcriber → AssemblyAI multilingual');
  console.log('  Voice → Azure Jenny Multilingual Neural');
  console.log('  First message → bilingual greeting\n');

  try {
    const result = await api('PATCH', `/assistant/${assistantId}`, payload);
    console.log('✅ Assistant updated successfully.');
    console.log(`   ID: ${result.id}`);
    console.log(`   Voice: ${result.voice?.provider}/${result.voice?.voiceId}`);
    console.log(`   Transcriber: ${result.transcriber?.provider}/${result.transcriber?.speechModel}`);
  } catch (err) {
    // If Azure voice fails, try fallback — maybe Azure needs credentials configured
    if (err.message.includes('credential') || err.message.includes('Azure')) {
      console.log(`⚠  Azure voice failed: ${err.message}`);
      console.log('   Falling back to 11Labs...\n');

      // Retry with 11Labs voice only
      delete payload.voice;
      try {
        const result = await api('PATCH', `/assistant/${assistantId}`, payload);
        console.log('✅ Assistant updated (without voice change).');
      } catch { console.error(`❌ Update failed entirely.`); process.exit(1); }
    } else {
      console.error(`❌ Update failed: ${err.message}`);
      process.exit(1);
    }
  }

  console.log('\nDone. Call +15206417021 to test the new greeting and Mandarin handling.');
}

main().catch(err => { console.error(`\nFATAL: ${err.message}`); process.exit(1); });
