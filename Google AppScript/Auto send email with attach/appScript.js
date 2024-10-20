var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
const SHT_ID = spreadsheet.getId();
Logger.log( SHT_ID );



// Get config from Sheet name and specify cells
const SHT = SpreadsheetApp.openById(SHT_ID).getSheetByName('Setting');

const isTest = SHT.getRange(1, 1).getValues()[0][0].toUpperCase() == 'TEST';

const isRunApp = SHT.getRange(2, 4).getValues()[0][0].toUpperCase() == 'YES';

const cronURL = SHT.getRange(4, 4).getValues()[0][0];

const cellTrigger = SHT.getRange(6, 4);
const isEnd = cellTrigger.getValues()[0][0] != '';
const deactiveTime = SHT.getRange(8, 4).getValues()[0][0];

const emails = SHT.getRange(10, 4).getValues()[0][0];
const subject = SHT.getRange(12, 4).getValues()[0][0];
const BODY = SHT.getRange(13, 4).getValues()[0][0];

const attach_sheet_id = SHT.getRange(15, 4).getValues()[0][0];

const chatId_token = SHT.getRange(17, 4).getValues()[0][0];
// Get config from Sheet name and specify cells




function myFunction() {
  sendTelegramMessage('test 123 456');
}


function doGet(event) {
  if( !isRunApp ){
    Logger.log('!isRunApp')
    return;
  }
  
  lastedActiveTime = UrlFetchApp.fetch(cronURL).getContentText();

  diffTime = ((+new Date()/1000) - lastedActiveTime)

  msg = 'Deactive time is not triggered';
  if( diffTime > deactiveTime && !isEnd || isTest ){
    res = sendEmailWithAttachment();
    sendTelegramMessage(res);
    msg = 'Deactive time is triggered';
    
    cellTrigger.setValue( 'Trigger on ' + (new Date()) );
  }

  var res = {success: true, message: msg, lastedActiveTime: lastedActiveTime, diffTime: diffTime, isEnd: isEnd};

  Logger.log( JSON.stringify(res) );
  return buildSuccessResponse(res);
}





function buildSuccessResponse(data) {
  var output = JSON.stringify(data);
  return ContentService.createTextOutput(output).setMimeType(ContentService.MimeType.JSON);
}

function sendTelegramMessage(text) {
  var sp = chatId_token.split('|');

  var apiUrl = 'https://api.telegram.org/bot' + sp[1] + '/sendMessage';
  var payload = {
    'method': 'post',
    'payload': {
      'chat_id': sp[0],
      'text': String(text)
    }
  };
  UrlFetchApp.fetch(apiUrl, payload);
}

function sendEmailWithAttachment() {
  try{
    var url = "https://docs.google.com/spreadsheets/d/" + SHT_ID + "/export?format=xlsx&gid=" + attach_sheet_id;
    var response = UrlFetchApp.fetch(url, {
      headers: {
        Authorization: 'Bearer ' + ScriptApp.getOAuthToken(),
      }
    });
    var blob = response.getBlob();
    blob.setName('attachment.xlsx');

    emails.split(/,/g).forEach(email=>{
      Logger.log( email.trim() );
      MailApp.sendEmail({
        to: email.trim(),
        subject: subject,
        htmlBody: "<br><pre>" + BODY + "</pre>",
        attachments: [blob]
      });
      Utilities.sleep(1000);
    });

    Logger.log("Email sent with attachment.");
    return "\n" + subject + "\n\nCheck email\n" + emails;
  }catch(e){
    Logger.log(e);
  }
}
