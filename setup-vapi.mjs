#!/usr/bin/env node
/**
 * Complete Vapi setup for SVICLOUD Phone Support Pilot.
 * Idempotent — resumes from existing .vapi-state.json when possible.
 *
 * Usage:
 *   VAPI_API_KEY=<key> node setup-vapi.mjs
 *
 * After script: configure Google Voice → Vapi number, then run pilot calls.
 */

import fs from 'fs';
import path from 'path';
import https from 'https';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const API_BASE = 'https://api.vapi.ai';
const FAQ_PATH = path.resolve(__dirname, 'docs/support-agent/support-faq.md');
const PROMPT_PATH = path.resolve(__dirname, 'docs/support-agent/vapi-assistant-prompt.md');
const STATE_PATH = path.resolve(__dirname, 'docs/support-agent/.vapi-state.json');
const ESCALATION_TOOL_SCHEMA_PATH = path.resolve(__dirname, 'docs/support-agent/vapi-escalation-tool.schema.json');
const AREA_CODE = '520';  // 702 unavailable, try 520 (Arizona)

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

function uploadFile(filePath) {
  return new Promise((resolve, reject) => {
    const key = process.env.VAPI_API_KEY;
    if (!key) { reject(new Error('VAPI_API_KEY not set')); return; }
    const boundary = `----VapiForm${Date.now()}`;
    const fc = fs.readFileSync(filePath, 'utf-8');
    const fn = encodeURIComponent(path.basename(filePath));
    const header = `--${boundary}\r\nContent-Disposition: form-data; name="file"; filename="${fn}"\r\nContent-Type: text/markdown\r\n\r\n`;
    const footer = `\r\n--${boundary}--\r\n`;
    const buf = Buffer.concat([Buffer.from(header, 'utf-8'), Buffer.from(fc, 'utf-8'), Buffer.from(footer, 'utf-8')]);
    const u = new URL('/file', API_BASE);
    const opts = { hostname: u.hostname, port: 443, path: u.pathname, method: 'POST',
      headers: { Authorization: `Bearer ${key}`, 'Content-Type': `multipart/form-data; boundary=${boundary}`,
        'Content-Length': buf.length } };
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
    req.write(buf);
    req.end();
  });
}

async function main() {
  console.log('=== Vapi Setup: SVICLOUD Phone Support Pilot ===\n');

  // Load or create state
  let state = {};
  try { state = JSON.parse(fs.readFileSync(STATE_PATH, 'utf-8')); } catch {}
  if (state.phoneNumberId && state.phoneNumber !== '(see dashboard)') {
    console.log('Already fully set up. Phone number:', state.phoneNumber);
    console.log('Re-run after deleting .vapi-state.json to start fresh.\n');
    printManualSteps(state.phoneNumber);
    return;
  }

  // Step 1: File upload (skip if we already have one)
  let fileId = state.fileId;
  if (!fileId) {
    console.log('[1/5] Uploading FAQ file...');
    if (!fs.existsSync(FAQ_PATH)) { console.error(`FAQ not found at ${FAQ_PATH}`); process.exit(1); }
    const file = await uploadFile(FAQ_PATH);
    console.log(`  ✅ File uploaded. ID: ${file.id}`);
    fileId = file.id;
  } else {
    console.log(`[1/5] FAQ file exists: ${fileId}`);
  }

  // Step 2: Read prompt
  const promptContent = fs.readFileSync(PROMPT_PATH, 'utf-8');

  // Step 3: Create assistant (skip if we already have one)
  let assistantId = state.assistantId;
  if (!assistantId) {
    console.log('[2/5] Creating assistant...');
    const assistant = await api('POST', '/assistant', {
      name: 'SVICLOUD Phone Support (Pilot)',
      model: {
        provider: 'openai',
        model: 'gpt-4.1-mini',
        temperature: 0.2,
        messages: [{ role: 'system', content: promptContent }],
      },
      voice: { provider: '11labs', voiceId: '21m00Tcm4TlvDq8ikWAM' },
      firstMessage: 'Thank you for calling SVICLOUD TV Box support. My name is Rachel — I can help in English or Mandarin. How can I assist you today?',
      firstMessageMode: 'assistant-speaks-first',
      maxDurationSeconds: 600,
      silenceTimeoutSeconds: 30,
      backgroundSound: 'office',
    });
    assistantId = assistant.id;
    console.log(`  ✅ Assistant created. ID: ${assistantId}`);
  } else {
    console.log(`[2/5] Assistant exists: ${assistantId}`);
  }

  // Step 4: Create query tool (skip if we already have one)
  let toolId = state.toolId;
  if (!toolId) {
    console.log('[3/5] Creating query tool with knowledge base...');
    const tool = await api('POST', '/tool', {
      type: 'query',
      messages: [
        { type: 'request-complete', content: 'I found the answer in our knowledge base.' },
        { type: 'request-failed', content: 'I could not find that information in my knowledge base. I will escalate your request to our support team.' },
      ],
      knowledgeBases: [{
        name: 'SVICLOUD Phone Support FAQ',
        provider: 'google',
        model: 'gemini-1.5-flash',
        description: 'Curated FAQ for the phone support pilot assistant',
        fileIds: [fileId],
      }],
      async: false,
    });
    toolId = tool.id;
    console.log(`  ✅ Query tool created. ID: ${toolId}`);

    // Attach tool to assistant
    await api('PATCH', `/assistant/${assistantId}`, {
      model: {
        provider: 'openai',
        model: 'gpt-4.1-mini',
        temperature: 0.2,
        messages: [{ role: 'system', content: promptContent }],
        toolIds: [toolId],
      },
    });
    console.log(`  ✅ Query tool attached to assistant.`);
  } else {
    console.log(`[3/5] Query tool exists: ${toolId}`);
  }

  // Step 4: Create escalation tool (skip if we already have one)
  let escalationToolId = state.escalationToolId;
  if (!escalationToolId) {
    console.log('[4/5] Creating escalation email tool...');
    if (!fs.existsSync(ESCALATION_TOOL_SCHEMA_PATH)) { console.error(`Escalation tool schema not found at ${ESCALATION_TOOL_SCHEMA_PATH}`); process.exit(1); }
    const schema = JSON.parse(fs.readFileSync(ESCALATION_TOOL_SCHEMA_PATH, 'utf-8'));
    const tool = await api('POST', '/tool', schema);
    escalationToolId = tool.id;
    console.log(`  ✅ Escalation tool created. ID: ${escalationToolId}`);
  } else {
    console.log(`[4/5] Escalation tool exists: ${escalationToolId}`);
  }

  await api('PATCH', `/assistant/${assistantId}`, {
    model: {
      provider: 'openai',
      model: 'gpt-4.1-mini',
      temperature: 0.2,
      messages: [{ role: 'system', content: promptContent }],
      toolIds: [toolId, escalationToolId].filter(Boolean),
    },
  });
  console.log(`  ✅ Tools attached to assistant.`);

  // Step 5: Create phone number
  console.log('[5/5] Creating Vapi phone number...');
  const phone = await api('POST', '/phone-number', {
    provider: 'vapi',
    numberDesiredAreaCode: AREA_CODE,
    assistantId: assistantId,
  });
  const phoneNumber = phone.phoneNumber || phone.number || '(see dashboard)';
  console.log(`  ✅ Phone number created: ${phoneNumber}`);
  console.log(`     ID: ${phone.id}`);

  // Save state
  state = { fileId, assistantId, toolId, escalationToolId, phoneNumberId: phone.id, phoneNumber, createdAt: new Date().toISOString() };
  fs.writeFileSync(STATE_PATH, JSON.stringify(state, null, 2));
  console.log('\nState saved to docs/support-agent/.vapi-state.json');

  printManualSteps(phoneNumber);
}

function printManualSteps(phoneNumber) {
  console.log('\n=== Manual Next Steps ===\n');
  console.log(`1. Google Voice > Settings > Forward calls to ${phoneNumber}`);
  console.log('   (Disable GV voicemail so Vapi picks up immediately)\n');
  console.log('2. Test by calling the Google Voice number from another phone\n');
  console.log('3. Run the 5 scenarios in docs/support-agent/pilot-test-calls.md');
  console.log('   and record results in the tables\n');
  console.log('4. Mark pilot acceptance when all 5 pass');
}

main().catch(err => { console.error(`\nFATAL: ${err.message}`); process.exit(1); });
