function doGet(e) {
    const dataRaw = e.parameter.data || null;
    const area = e.parameter.area || null;
    const searchCol = e.parameter.searchCol || null;
    const tabId = e.parameter.tabId || null;
    if (area == null || dataRaw == null || tabId == null) {
      return ContentService.createTextOutput('error. Missing info');
    }
    let data = JSON.parse(dataRaw);
  
    ///////// save all the provided dataif ("changed_people" in data) {
    if ("fox" in data) {
      if ("new_fox_data" in data.fox) {
        saveFoxData(area, data.fox.new_fox_data, tabId);
      }
    }
    if ("new_pranked_numbers" in data) {
      addPrankedNumbers(data.new_pranked_numbers, tabId);
    }
    if ("changed_people" in data) {
      if (data.changed_people.length > 0) {
        EditRowsInSheet(data.changed_people, tabId, parseInt(searchCol));
      }
    }
    return ContentService.createTextOutput("true");
  }
  function getSheetById(id) {
    return SpreadsheetApp.getActive().getSheets().filter(
      function(s) {return s.getSheetId() === id;}
    )[0];
  }
  
  function EditRowsInSheet(rows, tabId, searchColumn) { // these 2 functions were written by ChatGPT, with VERY VERY minor modifications
    var sheet = getSheetById(parseInt(tabId));
    
    var dataRange = sheet.getDataRange();
    var data = dataRange.getValues();
    var searchMap = createSearchMap(data, searchColumn);
    
    var updateValues = [];
    var updateLocations = [];
    
    // Iterate through the rows
    for (var i = 0; i < rows.length; i++) {
      var rowData = rows[i];
      var searchValue = rowData[searchColumn-1];
      
      // Check if the search value exists in the map
      if (searchMap.hasOwnProperty(searchValue)) {
        var rowIndex = searchMap[searchValue];
        
        // Update the row with the new data
        data[rowIndex] = rowData;
        
        // Store the updated row values for batch update
        updateValues.push(data[rowIndex]);
        updateLocations.push(rowIndex);
      }
    }
    
    // Update the sheet with the modified rows
    if (updateValues.length > 0) {
      for (var i = 0; i < updateValues.length; i++) {
        var updateRange = sheet.getRange(updateLocations[i]+1, 1, 1, updateValues[i].length);
        updateRange.setValues([updateValues[i]]);
      }
    }
  }
  
  function createSearchMap(data, searchColumn) {
    var searchMap = {};
    
    for (var i = 0; i < data.length; i++) {
      var row = data[i];
      var searchValue = row[searchColumn - 1];
      searchMap[searchValue] = i;
    }
    
    return searchMap;
  }
  
  function TEST_saveFoxData() {
    let area = 'Hägersten';
    let data = {
      'points' : 4573409534,
      'streak' : 3745395141
    };
    let tabId = '0';
    return saveFoxData(area, data, tabId);
  }
  function TEST_addPrankedNumbers() {
    return addPrankedNumbers(['1234567890'], '0');
  }
  
  function saveFoxData(area, data, tabId) {
    var sheet = getSheetById(parseInt(tabId));
  
    sheet.getRange('A1:D2').setValues([
      ['Fox', '', '', 'Prank List'],
      ['Data', 'Area', '', 'Data']
    ]);
    sheet.getRange('A1:2').setBackground('#d1d1d1');
  
    let foxing = sheet.getRange('A3:B').getValues().filter(x => (x[1]!=''));
    let sMap = createSearchMap(foxing, 2);
  
    sheet.getRange(sMap[area]+3, 1).setValue(JSON.stringify( data ));
  }
  
  function addPrankedNumbers(nums, tabId) {
    var sheet = getSheetById(parseInt(tabId));
  
    sheet.getRange('A1:D2').setValues([
      ['Fox', '', '', 'Prank List'],
      ['Data', 'Area', '', 'Data']
    ]);
    sheet.getRange('A1:2').setBackground('#d1d1d1');
  
    let rawCurrNums = sheet.getRange('D3').getValue();
    let currNumbers = [];
    if (rawCurrNums.includes('[')) {
      currNumbers = JSON.parse(rawCurrNums);
    }
    sheet.getRange('D3').setValue(JSON.stringify( currNumbers.concat(nums) ));
  }
  
  function fakeGet() {
    var eventObject = 
      {
        "parameter": {
          "area": "Kristianstad",
          "tabId": "1589745808",
          "data": '{ "changed_people":[["Missionary+Visit","38","2023-05-27+13:10:36","Not+sent","Hägersten","","Yellow","Yonike+Manga","Yonike","Manga","","yonikemunuo@yahoo.com","","Kristinehamn","68140","","fb","3+Free+Lessons+|+Jesus","","","","","23855061756770628","561826009044060","","",null]] }'
        },
        "contextPath": "",
        "contentLength": -1,
        "queryString": "action=view&page=3",
        "parameters": {
          "action": ["view"],
          "page": ["3"]
        }
      }
    doGet(eventObject);
  }
  