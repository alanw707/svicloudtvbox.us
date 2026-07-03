// Google Apps Script — Vapi Escalation Webhook
// Deploy as Web App → Execute as "Me" → Access "Anyone"
// Copy the deployment URL and give it to Alan

function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    
    const subject = `[Vapi Support] ${data.category || 'unknown'} — ${data.caller_name || 'No name'}`;
    const body = [
      '📞 New escalation from Vapi phone agent',
      '',
      '──────────────',
      `Caller:  ${data.caller_name || 'N/A'}`,
      `Phone:   ${data.caller_phone || 'N/A'}`,
      data.order_number ? `Order #: ${data.order_number}` : '',
      `Language: ${data.language || 'N/A'}`,
      `Category: ${data.category || 'N/A'}`,
      '──────────────',
      '',
      `Issue: ${data.issue_summary || 'N/A'}`,
      '',
      '— Vapi Phone Support (Pilot)',
    ].filter(Boolean).join('\n');
    
    GmailApp.sendEmail('support@svicloudtvbox.us', subject, body);
    
    return ContentService.createTextOutput(JSON.stringify({ status: 'ok' }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ status: 'error', message: err.message }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
