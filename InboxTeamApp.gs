function cellA1ToIndex(e,r){r=0==(r=r||0)?0:1;var n=e.match(/(^[A-Z]+)|([0-9]+$)/gm);if(2!=n.length)throw new Error("Invalid cell reference");e=n[0];return{row:rowA1ToIndex(n[1],r),col:colA1ToIndex(e,r)}}function colA1ToIndex(e,r){if("string"!=typeof e||2<e.length)throw new Error("Expected column label.");r=0==(r=r||0)?0:1;var n="A".charCodeAt(0),o=e.charCodeAt(e.length-1)-n;return 2==e.length&&(o+=26*(e.charCodeAt(0)-n+1)),o+r}function rowA1ToIndex(e,r){return e-1+(r=0==(r=r||0)?0:1)}
Array.prototype.indexOf2d = function(item) {
  for(var k = 0; k < this.length; k++){
    if(JSON.stringify(this[k]) == JSON.stringify(item)){
      return k;
    }
  }
}
function pasteFunction(x) {
  return JSON.stringify(SETPeopleData(JSON.parse(x))); 
}
function columnToLetter(column) {
  var temp, letter = '';
  while (column > 0)
  {
    temp = (column - 1) % 26;
    letter = String.fromCharCode(temp + 65) + letter;
    column = (column - temp - 1) / 26;
  }
  return letter;
}
///// function that the sheet itself is using
function anyReferrals(claimedCol, rowsToSearch, unclaimedData) {
  let out = claimedCol.slice(Math.max(claimedCol.length - rowsToSearch, 0));
  out = out.filter((x) => {
    return x[0] == unclaimedData;
  });
  //return JSON.stringify(out);
  return out.length > 0;
}
function makeRaw(x) {
  return JSON.stringify(x);
}
////////

function doGet(e) {
  const dataRaw = e.parameter.data || null;
  const area = e.parameter.area || null;
  if (area == null) {
    return ContentService.createTextOutput('error. Missing info');
  }

  var ss = SpreadsheetApp.getActive();
  var sheet = ss.getSheetByName("InboxAppHelper");

  let data = null;

  ///////// save all the provided data
  if (dataRaw != null) {
    data = JSON.parse(dataRaw);
    SETPeopleData(data.area_specific_data.my_referrals);
  }

  ///////// get all new data
  let newSyncData = {
    "overall_data" : {
      "referrals_available" : false,
      "new_referrals" : []
    },
    "area_specific_data" : {
      "my_referrals" : [],
      "last_sync" : new Date()
    }
  }
  newSyncData.overall_data.referrals_available = sheet.getRange('B5').getValue();
  newSyncData.overall_data.new_referrals = GETavailableRefs();
  newSyncData.area_specific_data.my_referrals = GETPeopleData(area);
  
  ///////// ship it off
  const output = JSON.stringify(newSyncData);
  return ContentService.createTextOutput(output).setMimeType(ContentService.MimeType.JSON);
}
function SETPeopleData(peopleList) {
  var ss = SpreadsheetApp.getActive();
  var sheet = null;

  const stupidPages = {
    "Mormons Bok Request" : ["", 1, 3, 2, 6, 12, 13, 14, 15, 16, 17, 18, 19, 21],
    "Missionary Visit Requests" : ["", 2, 14, 13, 15, 3, 4, 5, 8, 9, "", 6, 7, 10],
    "VTHOF leads" : ["", 2, 14, 13, 15, 3, 4, 5, 8, 9, "", 6, 7, 10]
  }

  //// switch to all inbox referrals sorted page
  
  // find each of the people
  for (let i = 0; i < peopleList.length; i++) {
    let thisPerson = peopleList[i];
    sheet = ss.getSheetByName(thisPerson[0]);

    // find the person
    const uniqueColInMainSheet = columnToLetter(stupidPages[thisPerson[0]][1]);
    const persRowNum = getPersonRow(sheet, uniqueColInMainSheet, thisPerson[1]);
    const rowLoc = String(persRowNum)+':'+String(persRowNum);
    const persRow = sheet.getRange(rowLoc).getValues()[0];

    // send off 1 at a time if unchanged
    const thisStupidList = stupidPages[thisPerson[0]];
    for(var k = 2; k < thisStupidList.length; k++){
      const ki = thisStupidList[k];
      if ( !(persRow[ki-1] == thisPerson[k] || persRow[ki-1] == null || persRow[ki-1] == "") ) {
        // set new data
        //return [k, persRow[ki-1], thisPerson[k], thisPerson];
        sheet.getRange(persRowNum, ki).setValue(thisPerson[k]);
      }
    }
    return "made it to end";
  }
}
function GETavailableRefs() {
  const area = 'Unclaimed';
  var ss = SpreadsheetApp.getActive();
  var sheet = ss.getSheetByName("InboxAppHelper");

  // get row 10
  const rten = sheet.getRange('10:10').getValues();

  // every 3 check if name is area
  let found = -1;
  for (let i = 0; i < rten[0].length; i+=3) {
    if (rten[0][i] == area) {
      found = i;
      break;
    }
  }
  if (found == -1) {
    return ['area_not_found', area, found];
  }
  found += 1;

  // get data below
  const thisRowsOfPeopleLoc = columnToLetter(found) + '11';
  return JSON.parse(sheet.getRange(thisRowsOfPeopleLoc).getValue());
}
function GETPeopleData(area) {
  var ss = SpreadsheetApp.getActive();
  var sheet = ss.getSheetByName("InboxAppHelper");

  // get row 10
  const rten = sheet.getRange('10:10').getValues();

  // every 3 check if name is area
  let found = -1;
  for (let i = 0; i < rten[0].length; i+=3) {
    if (rten[0][i] == area) {
      found = i;
      break;
    }
  }
  if (found == -1) {
    return ['area_not_found', area, found];
  }
  found += 1;

  // get data below
  const thisRowsOfPeopleLoc = columnToLetter(found) + '11';
  const thisRowsOfPeople = JSON.parse(sheet.getRange(thisRowsOfPeopleLoc).getValue());
  //return thisRowsOfPeople;

  const stupidPages = {
    "Mormons Bok Request" : [null, 1, 3, 2, 6, 12, 13, 14, 15, 16, 17, 18, 19, 21],
    "Missionary Visit Requests" : [null, 2, 14, 13, 15, 3, 4, 5, 8, 9, null, 6, 7, 10],
    "VTHOF leads" : [null, 2, 14, 13, 15, 3, 4, 5, 8, 9, null, 6, 7, 10]
  }


  //// switch to all inbox referrals sorted page
  
  let AllPeople = Array();
  // find each of the people
  for (let i = 0; i < thisRowsOfPeople.length; i++) {
    let thisPerson = thisRowsOfPeople[i];
    sheet = ss.getSheetByName(thisPerson[0]);

    // find the person
    const uniqueColInMainSheet = columnToLetter(stupidPages[thisPerson[0]][1]);
    const persRowNum = getPersonRow(sheet, uniqueColInMainSheet, thisPerson[1]);
    const persRow = sheet.getRange(String(persRowNum)+':'+String(persRowNum)).getValues()[0];
    //return persRow;
    
    // get their row data
    const thisStupidList = stupidPages[thisPerson[0]];
    let output = Array();
    output.push(thisPerson[0]);
    for(var k = 1; k < thisStupidList.length; k++){
      const ki = thisStupidList[k];
      output.push( (ki == null) ? '' : persRow[ki-1] );
    }
    AllPeople.push(output);
  }

  // return
  return AllPeople;
}

function getPersonRow(sheet, col, toSearch) {
  var data = sheet.getRange(col+':'+col).getValues().reverse();
  //return [toSearch, data];
  return data.length - data.indexOf2d([toSearch]);
}

/*
Working URL:
https://script.google.com/macros/s/AKfycbz5sdUaBw3uIK-TdMwxRgPcTTwDTT3PlVqlJZMVgqm8XIiraTtZRp_RySREpgx6aY4R/exec?area=Kristianstad&data={"overall_data":{"referrals_available":true,"new_referrals":[["Missionary Visit Requests","2023-01-29T09:24:02.000Z","Ulla Liinanki"],["Mormons Bok Request","2023-01-28T11:22:59.000Z","Barbro Karlsson"],["Mormons Bok Request","2023-01-28T09:28:40.000Z","Anders Tell"],["Mormons Bok Request","2023-01-27T22:31:24.000Z","Enita Parra"],["Missionary Visit Requests","2023-01-26T22:35:20.000Z","Kalle Henriksson"],["Missionary Visit Requests","2023-01-26T08:20:41.000Z","Annelie Strandberg"],["Missionary Visit Requests","2023-01-26T03:47:44.000Z","Dushyant Dwivedi"],["Missionary Visit Requests","2023-01-25T18:59:32.000Z","Thomas Grundwall"]]},"area_specific_data":{"my_referrals":[["Missionary Visit Requests","2023-01-24T09:55:33.000Z","Kristianstad","Not sent","Halmstad","Ewelina Opara","Ewelina","Opara",46735713445,"Laura.ch95@hotmsil.com","","Markaryd","","fb"]],"last_sync":"2023-01-31T12:29:56.994Z"}}

Working url that only reads:
https://script.google.com/macros/s/AKfycbydFKAmR2gzTtPswE9OWc43UbbTv8o4FxquxFmqmQkWQRY6C421VYjZYHA-nBFLSXeT/exec?area=Kristianstad
*/
