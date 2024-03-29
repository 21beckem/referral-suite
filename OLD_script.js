function inIframe() { try { return window.self !== window.top; } catch (e) { return true; } }
HTMLCollection.prototype.forEach = function (x) {
    return Array.from(this).forEach(x);
}
String.prototype.toTitleCase = function() {
    var splitStr = this.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        // You do not need to check if i is larger than splitStr length, as your for does that for you
        // Assign it back to the array
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
    }
    // Directly return the joined string
    return splitStr.join(' '); 
}
Array.prototype.indexOfAll = function (searchItem) {
    let i = this.indexOf(searchItem);
    let indexes = [];
    while (i !== -1) {
        indexes.push(i);
        i = this.indexOf(searchItem, ++i);
    }
    return indexes;
}
function verifySentInSMOEsAB(el) {
    setCookie('nextRedirectLoc', el.getAttribute('href'));
    let customAlert = new JSAlert("Have you already sent this person in the SMOEs Area Book? 👀");
    customAlert.addButton("Yes").then(function() {
        safeRedirect(getCookie('nextRedirectLoc'));
    });
    customAlert.addButton("No");
    customAlert.show();
}
function safeRedirect(ref) {
    if (!inIframe()) {
        window.location.href = ref;
        return;
    }
    window.parent.postMessage(JSON.stringify({
        type: 'redirect',
        data: ref,
    }), '*');
}
async function safeFetch(ref) {
    if (!inIframe()) {
        return await fetch(ref);
    }
    ref = window.parent.makeSafeLink(ref);
    // make link safe
    return await fetch(ref);
}
document.addEventListener('click', e => {
    const origin = e.target.closest('a');
    if (origin) {
        //console.log(origin);
        if (origin.hasAttribute("target") || !origin.hasAttribute("href")) {
            //JSAlert.alert('has target');
            return;
        }
        e.preventDefault();
        safeRedirect(origin.href);
    }
});
function setCookie(cname, cvalue) {
    localStorage.setItem(cname, cvalue);
}
function getCookie(cname) {
    return localStorage.getItem(cname);
}
function getCookieJSON(x) {
    let out = null;
    try {
        out = JSON.parse(getCookie(x));
    } catch (e) { }
    return out;
}
function setCookieJSON(x, y) {
    return setCookie(x, JSON.stringify(y));
}
function saveBeforeInfoPage(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
}
function clearAllLocalSyncDataAndSync() {
    let customAlert = new JSAlert("Are you sure? This will erase all locally changed data and force Referral Suite to re-sync.");
    customAlert.addButton("Yes").then(function() {
        SheetMap.load();
        SheetMap.setChangedCells({});
        localStorage.removeItem('localFox');
        localStorage.removeItem('debug-mode');
        setCookie('dataSync', null);
        //setCookie('areaUser', '');
        //setCookie('areaUserEmail', '');
        safeRedirect('force-sync.html');
    });
    customAlert.addButton("No");
    customAlert.show();
}
function setConsoleDotLogToDebug() {
    window.console.log = function () {
        if (_('debug-table')) {
            let argsArr = Array();
            let OBJ_ids = Array();
            for (const message of arguments) {
                if (typeof message == 'object') {
                    let i = window.numForLoggingThings || 0;
                    argsArr.push( '<div id="loggingOBJs_'+i+'"><div>' );
                    OBJ_ids.push(['loggingOBJs_'+i, message]);
                    window.numForLoggingThings = i + 1;
                } else {
                    argsArr.push( message );
                }
            }
            const DB = _('debug-table');
            DB.classList.add('active');
            DB.innerHTML += `<div class="item">
                <div class="msg">` + argsArr.join(', ') + `</div>
                <div class="link">log</div>
            </div>`;
            OBJ_ids.forEach((x)=> {
                new JsonViewer({
                    value: x[1]
                }).render(x[0]);
            });
        }
    }
}
function _(x) { return document.getElementById(x); }
function _CONFIG() {
    return getCookieJSON('CONFIG') || null;
}
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
/////  above functions make everything function with basic navigation between pages. No Touch!
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
let data = getCookieJSON('dataSync') || null;
let area = getCookie('areaUser') || null;
let su_refs = getCookieJSON('suSync') || null;
let su_done = getCookieJSON('suDone') || [];
let CONFIG = getCookieJSON('CONFIG') || null;
let ITLs = (getCookie('areaIsLeaders') == "1");
const DEBUG_MODE = localStorage.getItem('debug-mode')=='true' || false;
const FoxEnabled = (document.currentScript.getAttribute('no-fox') == null && CONFIG != null && CONFIG['InboxFox']['enable']);

if (DEBUG_MODE) {
    window.onerror = function(msg, url, linenumber) {
        if (_('debug-table')) {
            const DB = _('debug-table');
            DB.classList.add('active');
            DB.innerHTML += `<div class="item">
                <div class="msg">` + msg + `</div>
                <div class="link">` + url.replace(/^.*[\\\/]/, '') + ':' + String(linenumber) + `</div>
            </div>`;
        }
        return false;
    }
}

if ((area == null || CONFIG == null) && document.currentScript.getAttribute('dont-redirect') == null) {
    safeRedirect('login.html');
} else {
    if (sessionStorage.getItem("logged_in") == null && document.currentScript.getAttribute('dont-redirect') == null) {
        safeRedirect('pin-code.html');
    }
}


async function SYNC(loadingCover = true) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (loadingCover) {
        _('loadingcover').style.display = '';
    }

    // if debug mode, print EVERYTHING
    if (DEBUG_MODE) {
        //setConsoleDotLogToDebug();
    }

    await SYNC_getConfig();

    moveAllChangedDataToASeparateAreaOfData();

    let rs_wait = SYNC_referralSuiteStuff();
    let sm_wait = SYNC_sheetMapStuff();
    let al_wait = SYNC_getMissionAreasList();
    let pl_wait = SYNC_getPrakedNumbersList();

    await rs_wait;
    await sm_wait;
    await al_wait;
    await pl_wait;

    if (_CONFIG()['overall settings']['tell backend system which area is inboxing']) {
        await SYNC_setCurrentInboxingArea();
    }

    sortSyncDataByDates();
    
    saveUnchangedSyncData();
    
    await SYNC_foxVars();

    //take away overlay
    if (loadingCover) {
        _('loadingcover').style.display = 'none';
    }
}
function sortSyncDataByDates() {
    let thisData = getCookieJSON('dataSync');
    thisData.area_specific_data.my_referrals = thisData.area_specific_data.my_referrals.sort((first, second) => new Date(second[TableColumns['date']]) - new Date(first[TableColumns['date']]));
    thisData.overall_data.follow_ups = thisData.overall_data.follow_ups.sort((first, second) => new Date(first[TableColumns['next follow up']]) - new Date(second[TableColumns['next follow up']]));
    setCookieJSON('dataSync', thisData);
}
function saveUnchangedSyncData() {
    setCookieJSON('unchangedSyncData', getCookieJSON('dataSync'));
}
function moveAllChangedDataToASeparateAreaOfData() {
    if (data == null) {
        return;
    }
    const unchangedSyncData = getCookieJSON('unchangedSyncData');
    if (!data.hasOwnProperty('changed_people')) {
        data.changed_people = Array();
    }
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const rockPerson = unchangedSyncData.area_specific_data.my_referrals[i];
        const changedPerson = data.area_specific_data.my_referrals[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) !== JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }
    for (let i = 0; i < data.overall_data.follow_ups.length; i++) {
        const rockPerson = unchangedSyncData.overall_data.follow_ups[i];
        const changedPerson = data.overall_data.follow_ups[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) !== JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }
    for (let i = 0; i < data.overall_data.new_referrals.length; i++) {
        const rockPerson = unchangedSyncData.overall_data.new_referrals[i];
        const changedPerson = data.overall_data.new_referrals[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) !== JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }

    //fox
    if (JSON.stringify(unchangedSyncData.fox) !== JSON.stringify(data.fox)) {
        let strEn = JSON.stringify(data.fox);
        data.fox.new_fox_data = CryptoJS.AES.encrypt(strEn, area) + '';
    }
    console.log('old', getCookieJSON('dataSync'));
    console.log('new', data);
    setCookieJSON('dataSync', data);
}
async function SYNC_getMissionAreasList() {
    return await safeFetch('mission_specific_editable_files/mission_areas_list.txt')
        .then((response) => response.text())
        .then((txt) => {
            let areas = txt.split('\n');
            areas = areas.map(x => {
                return x.split(',').map(xx => {
                    return xx.trim();
                });
            });
            setCookieJSON('missionAreasList', areas);
        });
}
async function SYNC_getPrakedNumbersList() {
    const rawReadOnlyLink = _CONFIG()['overall settings']['readonly data sheet link'];
    let readOnlyId = new URLSearchParams(new URL(rawReadOnlyLink).hash.replace('#', '?')).get('gid');
    let readonlyLink = rawReadOnlyLink.substr(0, rawReadOnlyLink.lastIndexOf("/"));
    let prankArr = await G_Sheets_Query(readonlyLink, readOnlyId, 'SELECT *', 'D3:E');

    //convert list to object
    let prankNumberList = {};
    prankArr.forEach((v) => {
        prankNumberList[ v[0] ] = v[1];
    });
    setCookieJSON('prankNumberList', prankNumberList);
}
function showPrankedNumberInfoBox(date) {
    JSAlert.alert('This number has been marked as pranked on ' + date + '. Proceed with caution; trust the spirit.');
}
async function SYNC_getConfig() {
    return await safeFetch('mission_specific_editable_files/config.json')
        .then((response) => response.json())
        .then((json) => {
            setCookieJSON('CONFIG', json);
            if (area != null) {
                setCookie('areaUserEmail', json['inboxers'][area]['email']);
                leaders = (json['follow ups']['area to take care of dead inboxing area follow ups'] == area) ? '1' : '0';
                setCookie('areaIsLeaders', leaders);
            }
            return json;
        });
}
async function SYNC_referralSuiteStuff() {
    if (data != null) {
        delete data.overall_data;
        delete data.area_specific_data;
    }
    if (_CONFIG()['overall settings']['table type'].toLowerCase().includes('sql')) {
        await sortOfSYNC_UseSQL();
    } else {
        await sortOfSYNC_QueryMyself();
    }
    console.log('done: SYNC_referralSuiteStuff');
}
function needScribeReadonlyThings() {
    if (data == null) { return false; }
    return ("fox" in data && "new_fox_data" in data.fox) ||
        ("new_pranked_numbers" in data && data.new_pranked_numbers.length > 0);
}
async function sortOfSYNC_QueryMyself() {
    const rawLink = _CONFIG()['overall settings']['table Query link'];
    const rawReadOnlyLink = _CONFIG()['overall settings']['readonly data sheet link'];
    let qURL = rawLink.substr(0, rawLink.lastIndexOf("/"));
    let tabId = new URLSearchParams(new URL(rawLink).hash.replace('#', '?')).get('gid');
    let readOnlyId = new URLSearchParams(new URL(rawReadOnlyLink).hash.replace('#', '?')).get('gid');
    let sURL = _CONFIG()['overall settings']['table scribe link'];
    sURL += '?area=' + area;
    sURL += '&tabId=' + tabId;
    sURL += '&searchCol=' + _CONFIG()['tableColumns']['id'];
    sURL += '&readOnlyId=' + readOnlyId;
    sURL += '&data=' + encodeURIComponent(JSON.stringify(data));


    if (data != null && ("changed_people" in data && data.changed_people.length > 1) || needScribeReadonlyThings()) {
        console.log('scribe activated', sURL);
        await safeFetch(sURL);
    }


    let newSyncData = {
        "fox": {},
        "overall_data": {
            "new_referrals": [],
            "follow_ups": []
        },
        "area_specific_data": {
            "my_referrals": [],
            "last_sync": new Date()
        }
    }
    let claimedCol = GoogleColumnToLetter(_CONFIG()['tableColumns']['claimed area'] + 1);
    let sentStatusCol = GoogleColumnToLetter(_CONFIG()['tableColumns']['sent status'] + 1);

    // read unclaimed
    let newRefs_wait = G_Sheets_Query(qURL, tabId, "select * where " + claimedCol + " = 'Unclaimed'");

    let readonlyLink = rawReadOnlyLink.substr(0, rawReadOnlyLink.lastIndexOf("/"));
    let areaFoxStat_wait = G_Sheets_Query(readonlyLink, readOnlyId, 'SELECT * WHERE B="'+area+'"', 'A3:B');

    // read for this area
    let myFers_wait = G_Sheets_Query(qURL, tabId, "select * where " + claimedCol + " = '" + area + "' AND " + sentStatusCol + " = 'Not sent'");

    if (_CONFIG()['follow ups']['enable follow ups']) {

        // read ALL follow ups
        let nxtFU_Col = GoogleColumnToLetter(_CONFIG()['tableColumns']['next follow up'] + 1);
        let FUs = await G_Sheets_Query(qURL, tabId, "select * where " + nxtFU_Col + " < now() and " + nxtFU_Col + " is not null");

        // filter through follow ups. Keep those that don't have a team anymore to the first leader in the list
        for (let i = 0; i < FUs.length; i++) {
            const per = FUs[i];
            let per_claimed = per[_CONFIG()['tableColumns']['claimed area']];
            if (per_claimed == area) {
                newSyncData.overall_data.follow_ups.push(per);
                continue;
            }
            let areaIsITLs = _CONFIG()['follow ups']['area to take care of dead inboxing area follow ups'] == area;
            if (!Object.keys(_CONFIG()['inboxers']).includes(per_claimed) && areaIsITLs) {
                newSyncData.overall_data.follow_ups.push(per);
                continue;
            }
        }
    }
    // wait for all fetches to finish
    newSyncData.overall_data.new_referrals = await newRefs_wait;
    newSyncData.area_specific_data.my_referrals = await myFers_wait;
    newSyncData.fox = decodeFox((await areaFoxStat_wait)[0]);

    console.log('newly received package', newSyncData);

    setCookieJSON('dataSync', newSyncData);
}
function decodeFox(arr) {
    const defaultFox = {
        "points" : 0,
        "streak" : []
    };
    let restart = false;
    if (arr == undefined) {
        return defaultFox;
    }
    if (arr.length == 0) {
        restart = true;
    } else if (arr[0] == '') {
        restart = true;
    }
    if (restart) {
        return defaultFox;
    } else {
        try {
            const j = JSON.parse( CryptoJS.AES.decrypt(arr[0], arr[1]).toString(CryptoJS.enc.Utf8) );
            delete j.new_points;
            delete j.new_streak;
            return j;
        } catch (e) {
            return defaultFox;
        }
    }
}
function GoogleColumnToLetter(column) {
    var temp, letter = '';
    while (column > 0) {
        temp = (column - 1) % 26;
        letter = String.fromCharCode(temp + 65) + letter;
        column = (column - temp - 1) / 26;
    }
    return letter;
}
async function G_Sheets_Query(mainLink, tabId, query, range=null) {
    let qLink = mainLink + '/gviz/tq?tq=' + encodeURIComponent(query) + '&gid=' + tabId;
    if (range != null) {
        qLink += '&range=' + range;
    }
    console.log("G_querying:", qLink);
    return await safeFetch(qLink)
        .then((response) => response.text())
        .then((txt) => {
            //console.log('G_query-res', txt);
            return getRowsFromQuery(JSON.parse(
                txt.replace("/*O_o*/\n", "") // remove JSONP wrapper
                    .replace(/(google\.visualization\.Query\.setResponse\()|(\);)/gm, "") // remove JSONP wrapper
            ));
        })
}
function getRowsFromQuery(bigData) {
    return bigData.table.rows.map(x => {
        return x['c'].map(xx => {
            if (xx == null) { return '' }
            if ('f' in xx) { return xx['f'] }
            return xx['v'];
        })
    })
}
async function sortOfSYNC_UseSQL() {
    let fetchURL = _CONFIG()['overall settings']['table Query link'];
    fetchURL += '?area=' + area;
    fetchURL += '&searchCol=' + _CONFIG()['tableColumns']['id'];
    fetchURL += (data == null) ? '' : '&data=' + encodeURIComponent(JSON.stringify(data));


    console.log('SQL Fetch:', fetchURL);
    console.log('Payload:', JSON.stringify(data));
    let response_SQL_wait = safeFetch(fetchURL);
    
    //update readonly stuff
    if (needScribeReadonlyThings()) {
        const rawLink = _CONFIG()['overall settings']['table Query link'];
        const rawReadOnlyLink = _CONFIG()['overall settings']['readonly data sheet link'];
        let tabId = new URLSearchParams(new URL(rawLink).hash.replace('#', '?')).get('gid');
        let readOnlyId = new URLSearchParams(new URL(rawReadOnlyLink).hash.replace('#', '?')).get('gid');
        let sURL = _CONFIG()['overall settings']['table scribe link'];
        sURL += '?area=' + area;
        sURL += '&tabId=' + tabId;
        sURL += '&searchCol=' + _CONFIG()['tableColumns']['id'];
        sURL += '&readOnlyId=' + readOnlyId;
        delete data.changed_people;
        sURL += '&data=' + encodeURIComponent(JSON.stringify(data));

        console.log('scribe activated', sURL);
        await safeFetch(sURL);
    }

    // get all the readonly stuff
    const rawReadOnlyLink = _CONFIG()['overall settings']['readonly data sheet link'];
    let readOnlyId = new URLSearchParams(new URL(rawReadOnlyLink).hash.replace('#', '?')).get('gid');
    let readonlyLink = rawReadOnlyLink.substr(0, rawReadOnlyLink.lastIndexOf("/"));
    let areaFoxStat_wait = G_Sheets_Query(readonlyLink, readOnlyId, 'SELECT * WHERE B="'+area+'"', 'A3:B');
    
    // wait for all fetches to finish
    let response = await response_SQL_wait;
    let syncRes = await response.json();
    
    // sort through which follow ups we should have
    let newFUs = Array();
    for (let i = 0; i < syncRes.overall_data.follow_ups.length; i++) {
        const per = syncRes.overall_data.follow_ups[i];
        let per_claimed = per[_CONFIG()['tableColumns']['claimed area']];
        if (per_claimed == area) {
            newFUs.push(per);
            continue;
        }
        let areaIsITLs = _CONFIG()['follow ups']['area to take care of dead inboxing area follow ups'] == area;
        if (!Object.keys(_CONFIG()['inboxers']).includes(per_claimed) && areaIsITLs) {
            newFUs.push(per);
            continue;
        }
    }
    syncRes.overall_data.follow_ups = newFUs;
    syncRes.fox = decodeFox((await areaFoxStat_wait)[0]);

    console.log('newly received package', syncRes);
    //save to cookie
    setCookieJSON('dataSync', syncRes);
}
async function SYNC_sheetMapStuff() {
    // sync schedule changes then get updated stuff:
    const ss = new SheetMap(CONFIG['schedule settings']);
    await SheetMap.syncChanges();
    await ss.fetch(CONFIG['schedule settings']['tab name'], CONFIG['schedule settings']['schedule range']);
}
function GetTodaysSchedule() {
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    let niceDate = dayNames[new Date().getDay()] + '\n' + monthNames[new Date().getMonth()] + ' ' + String(new Date().getDate());
    SheetMap.load();
    let iOfToday = SheetMap.vars.tableDataNOW[0].indexOf(niceDate);
    return SheetMap.vars.tableDataNOW.map(x => x[iOfToday]);
}
function getCurrentInboxingArea() {
    SheetMap.load();
    let dagensSchedule = GetTodaysSchedule();
    let scheduleTimes = SheetMap.vars.tableDataNOW.map(x => [x[0], x[1]]);

    const d = new Date();
    const time_beginning = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    for (let i = 0; i < dagensSchedule.length; i++) {
        const dateFrom = new Date(time_beginning + ' ' + scheduleTimes[i][0].trim() + ':00.000');
        const dateTo = new Date(time_beginning + ' ' + scheduleTimes[i][1].trim() + ':00.000');
        if (d.getTime() <= dateTo.getTime() && d.getTime() >= dateFrom.getTime()) {
            return dagensSchedule[i];
        }
    }
    return '';
}
async function SYNC_setCurrentInboxingArea() {
    let thisArea = getCurrentInboxingArea();

    let areaEmail = _CONFIG()['inboxers'][0];
    const reqUrl = _CONFIG()['overall settings']['table scribe link'] + '?currentInboxer=' + encodeURI(thisArea) + '&email=' + encodeURI(areaEmail);
    await safeFetch(reqUrl);
}
async function INSTANTSYNC_pingNF(thisDebug=false) {
    if (CONFIG['overall settings']['table type'].toLowerCase().includes('sql')) {
        // SQL
        let fetchURL = CONFIG['overall settings']['table Query link'] + '?pingNF=1';
        if (thisDebug) {
            console.log(fetchURL);
        }
        return await safeFetch(fetchURL)
            .then((response) => response.text())
            .then((txt) => {
                //console.log('G_query-res', txt);
                return parseInt(txt) > 0 && data.overall_data.new_referrals.length == 0;
            });
    } else {
        // Google Sheets
        const rawLink = CONFIG['overall settings']['table Query link'];
        let thisId = new URLSearchParams(new URL(rawLink).hash.replace('#', '?')).get('gid');
        let thisLink = rawLink.substr(0, rawLink.lastIndexOf("/"));
        let claimedCol = GoogleColumnToLetter(TableColumns['claimed area'] + 1);
        let res = await G_Sheets_Query(thisLink, thisId, "select * where " + claimedCol + " = 'Unclaimed'");
        return res.length > 0;
    }
}
function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        let dotStyle = `<div class="w3-bar-item w3-circle">
            <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
        </div>`;
        const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="claim_the_referral.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">` + dotStyle + `
            <div class="w3-bar-item">
              <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('unclaimedlist').innerHTML = output;
}
function makeListClaimedPeople(arr) {
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        let dotStyle = `<div class="w3-bar-item w3-circle">
            <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>`;
        let nextPage = 'contact_info.html';
        if (!hasPersonBeenContactedToday(per)) {
            dotStyle += `<div class="w3-left-align w3-circle" style="position:relative; color:red; right:-18px; top:-36px; font-size:25px; font-weight:bold; height:0;">!</div>`;
        }
        dotStyle += `</div>`;
        const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="` + nextPage + `" class="person-to-click">
          <div class="w3-bar" style="display: flex;">` + dotStyle + `
            <div class="w3-bar-item">
              <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('yourreferrals').innerHTML = output;
}
function makeListFollowUpPeople(arr) {
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        const elapsedTime = timeSince_formatted(new Date(per[TableColumns['next follow up']]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="follow_up_on.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 27px;">
                <i class="fa fa-calendar-check-o" style="color:#1d53b7; font-size:22px"></i>
              </div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[TableColumns['teaching area']] + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('yourfollowups').innerHTML = output;
}
function fillInFHInfo() {
    const person = idToReferral(getCookieJSON('linkPages'));
    _('personName').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
    //_('contactname').innerHTML = _('personName').innerHTML;
    _('email').innerHTML = person[TableColumns['email']];
    _('address').innerHTML = person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
    _('FH_lang').innerHTML = CONFIG['overall settings']['most common language in mission'];
    _('FH_message').innerHTML = makeFHMessage(person);
}
function makeFHMessage(per) {
    if (per[TableColumns['referral origin']].toLowerCase().includes('fb') || per[TableColumns['referral origin']].toLowerCase().includes('ig')) {
        return `This is a FAMILY HISTORY REFERRAL from Facebook!! This person clicked on a FB ad and wants help with Family History! Contact them as as soon as possible. USE EMAIL!

GOOD LUCK!

What they want help with: ` + per[TableColumns['help request']] + `

How experienced they are: ` + per[TableColumns['experience']];
    } else {
        return `This is a FAMILY HISTORY REFERRAL from the MISSION WEBSITE!! This person went to the website and wants help with Family History! Contact them as as soon as possible. USE EMAIL!

GOOD Luck!

What they want help with: ` + per[TableColumns['help request']] + `

How experienced they are: ` + per[TableColumns['experience']];
    }
}
function getOldestClaimedPerson() {
    let peeps = data.area_specific_data.my_referrals;
    let currentOldest = peeps[0];
    for (let i = 0; i < peeps.length; i++) {
        const per = peeps[i];
        if (new Date(per[TableColumns['date']]).getTime() < new Date(currentOldest[TableColumns['date']]).getTime()) {
            currentOldest = per;
        }
    }
    return currentOldest;
}
async function searchAndDisplayDatabaseReferrals() {
    _('loadingAnim').style.display = '';
    const searchQ = _('referralSearchbar').value;

    const returnedRefs = await getReferralsFromDatabase(searchQ);

    let output = '';
    for (let i = 0; i < returnedRefs.length; i++) {
        const per = returnedRefs[i];
        output += `<div class="searchResult" onclick="viewPersonInfo(` + JSON.stringify(per).replaceAll("'", "\\'").replaceAll('"', "'") + `)">
              <a class="name">` + per[TableColumns['first name']] + per[TableColumns['last name']] + `</a>
              <table style="width: 100%;">
                <tr>
                  <td>
                    <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-clock" style="color: var(--light-blue);"></i> ` + per[TableColumns['date']] + `</div>
                  </td>
                  <td>
                    <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-signal" style="color: var(--call-color);"></i> ` + per[TableColumns['sent status']] + `</div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-reply-all" style="color: var(--sms-color);"></i> ` + per[TableColumns['type']] + `</div>
                  </td>
                  <td>
                    <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-chalkboard-user" style="color: var(--red);"></i> ` + per[TableColumns['teaching area']] + `</div>
                  </td>
                </tr>
              </table>
          </div>`;
    }

    if (returnedRefs.length == 0) {
        output = 'No results';
    } else if (output == '') {
        output = "There's been an error...";
    }
    _('loadingAnim').style.display = 'none';
    _('searchResultsBox').innerHTML = output;
}
function viewPersonInfo(per) {
    setCookieJSON('personJSON', per);
    safeRedirect('view_full_person_info.html');
}
async function getReferralsFromDatabase(searchQ) {
    // return data.area_specific_data.my_referrals;

    if (!_CONFIG()['overall settings']['table type'].toLowerCase().includes('sql')) {
        const rawLink = CONFIG['overall settings']['table Query link'];
        let qURL = rawLink.substr(0, rawLink.lastIndexOf("/"));
        let tabId = new URLSearchParams(new URL(rawLink).hash.replace('#', '?')).get('gid');
        return await G_Sheets_Query(qURL, tabId, 'select * where * contains "' + searchQ.replaceAll('"', '\\"') + '" limit 50');
    } else {
        let fetchURL = CONFIG['overall settings']['table Query link'] + '?area=SuperCoolAndSecretQuery&q=' + encodeURIComponent(searchQ);

        const response = await safeFetch(fetchURL);
        return await response.json();
    }

}
function fillInViewFullPersonInfo() {
    const per = getCookieJSON('personJSON');
    let tableCols = [];
    for (let i = 0; i < Object.keys(TableColumns).length; i++) {
        tableCols.push([Object.keys(TableColumns)[i], TableColumns[Object.keys(TableColumns)[i]]]);
    }
    tableCols = tableCols.sort((first, second) => first[1] - second[1]);

    let output = '';
    for (let i = 0; i < tableCols.length; i++) {
        const COL = tableCols[i];
        let val = (per[COL[1]] == '') ? 'Undefined' : per[COL[1]];
        output += `
        <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
            <div class="w3-left-align w3-small w3-opacity">` + COL[0] + `</div>
            <div id="referraltype" class="w3-left-align w3-large">` + val + `</div>
        </div>`;
    }
    _('fullInfoResultsBox').innerHTML = output;
}
function fillInHomePage() {
    let maxRefsAllowed = 15;
    const currentRefCount = data.area_specific_data.my_referrals.length;


    let totReferralsBar = _("totReferralsBar");
    let totReferrals = _("totReferrals");

    totReferralsBar.style.width = Math.min(currentRefCount / maxRefsAllowed * 100, 100) + '%';

    totReferrals.innerHTML = currentRefCount + '/' + maxRefsAllowed;

    if (currentRefCount >= maxRefsAllowed) {
        totReferrals.classList.add('w3-text-red');
    }
    let maxRefAge = 7;
    if (data.area_specific_data.my_referrals.length > 0) {
        let oldReferralBar = _("oldReferralBar");
        let oldReferral = getOldestClaimedPerson()[TableColumns['date']];
        let today = new Date();
        let oldDate = new Date(oldReferral);
        let dayDifference = Math.round((today.getTime() - oldDate.getTime()) / (1000 * 60 * 60 * 24));
        oldReferralBar.style.width = Math.min(dayDifference / maxRefAge * 100, 100) + '%';

        _("agebyday").innerHTML = dayDifference + '/' + maxRefAge;

        if (dayDifference >= maxRefAge) {
            _("agebyday").classList.add('w3-text-red');
        }
    } else {
        _("agebyday").innerHTML = '0/' + maxRefAge;
    }
    _('MB_deliverLink').href = CONFIG['home page links']['book of mormon delivery form'];
    _('adDeck').href = CONFIG['home page links']['ad deck'];
    _('gToBusSuite').href = CONFIG['home page links']['business suite help'];
    setHomeBigBtnLink('1_sync');
    setHomeBigBtnLink('2_contact');
    setHomeBigBtnLink('3_log');
    setHomeBigBtnLink('4_message');
    setHomeBigBtnLink('5_comments');

    let streakBoxFilter = '';
    switch (foxStreakExtendingStatus()) {
        case 'done for today':
            streakBoxFilter = '';
            break;
        case 'can extend':
            streakBoxFilter = 'grayscale(0.8) opacity(0.8)';
            break;
    
        default:
            streakBoxFilter = 'brightness(0.5) grayscale(1) opacity(0.2)';
    }
    _('streakBox').style.filter = streakBoxFilter;
    _('streakBoxNum').innerHTML = data.fox.streak.length;
    _('inbucksValue').innerHTML = data.fox.points;
}
function sendToReportingForm() {
    if (!localFox.dailyPointsReceived.includes('reported')) {
        setAddFoxPoints(5);
        localFox.dailyPointsReceived.push('reported');
        setCookieJSON('localFox', localFox);
        setCookieJSON('dataSync', data);
    }
    let link = CONFIG['home page links']['6_report'].replace("{Area}", area);
    window.open(link, '_BLANK')
}
function fillInContactInfo() {
    const person = idToReferral(getCookieJSON('linkPages'));
    _('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
    //_('telnumber').href = 'tel:+' + person[ TableColumns['phone'] ];
    //_('smsnumber').href = 'sms:+' + person[ TableColumns['phone'] ];
    //_('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';
    const numb = person[TableColumns['phone']].trim();

    _('referraltype').innerHTML = person[TableColumns['type']].replaceAll('_', ' ');
    _('referralorigin').innerHTML = prettyPrintRefOrigin(person[TableColumns['referral origin']]);
    if (numb == '') {
        // no number
        _('phonenumber').innerHTML = 'Undefined';
        _('telnumber').classList.add('disabled');
        _('smsnumber').classList.add('disabled');
    } else if (getCookieJSON('prankNumberList').hasOwnProperty(numb)) {
        let onclickFunc = "showPrankedNumberInfoBox('" + getCookieJSON('prankNumberList')[numb] + "')";
        _('phonenumber').innerHTML = '<span class="prankedNumberWarning">' + numb + '</span> <i class="fa-solid fa-circle-question" onclick="'+onclickFunc+'"></i>';
    } else {
        _('phonenumber').innerHTML = numb;
    }
    _('email').innerHTML = person[TableColumns['email']];
    let addStr = person[TableColumns['street address']] + ' ' + person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
    _('address').innerHTML = addStr;
    _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
    _('adName').innerHTML = person[TableColumns['ad name']];
    _('adDeck').href = CONFIG['home page links']['ad deck'];
    _('prefSprak').innerHTML = (person[TableColumns['lang']] == "") ? "Undeclared" : person[TableColumns['lang']];
    if (person[TableColumns['type']].toLowerCase().includes('family history')) {
        _('sendReferralBtn').setAttribute('onclick', "safeRedirect('fh_referral_info.html')");
    }
    fillInAttemptLog();
}
function openGoogleSlides(link) {
    setCookie('openThisSlides', link);
    safeRedirect('view_google_slides.html');
}
function setHomeBigBtnLink(elId) {
    let link = CONFIG['home page links'][elId];
    const el = _(elId);
    if (link.includes('www.canva.com') || link.includes('docs.google.com/presentation')) {
        el.setAttribute('onclick', "openGoogleSlides('" + link + "')");
    } else if (!link.startsWith('http')) {
        el.href = link;
    } else {
        console.log('Unrecognized presentation link. Will open in new tab:' + link);
        el.href = link.replace("{Area}", area);
        el.setAttribute('target', '_blank');
    }
}
function callThenGoBack() {
    const person = idToReferral(getCookieJSON('linkPages'));
    logAttempt(0);
    localStorage.setItem('justAttemptedContact', '1');
    window.open('tel:+' + person[TableColumns['phone']], '_blank');
    safeRedirect('contact_info.html');
}
function fillInHelpBeforeCallPage() {
    const person = idToReferral(getCookieJSON('linkPages'));
    let link = CONFIG['tips before calling'][person[TableColumns['type']]];

    if (link.includes('www.canva.com')) {
        link = link.substr(0, link.lastIndexOf("/")) + '/view?embed';
    } else if (link.includes('docs.google.com')) {
        link = link.substr(0, link.lastIndexOf("/")) + '/embed';
    } else {
        console.error('Unrecognized presentation link. Will open in new tab:' + link);
    }

    _('google_slides_import').src = link;
}
function fillInFollowUpInfo() {
    const person = data.overall_data.follow_ups[getCookieJSON('linkPages')];
    _('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
    _('referraltype').innerHTML = person[TableColumns['type']].replaceAll('_', ' ');
    _('lastAtt').innerHTML = new Date(person[TableColumns['sent date']]).toLocaleDateString("en-US", { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    let fuTimes = person[TableColumns['amount of times followed up']];
    _('followUpCount').innerHTML = fuTimes + ((parseInt(fuTimes) == 1) ? ' time' : ' times');
    _('refLoc').innerHTML = person[TableColumns['teaching area']];
    _('refLoc2').innerHTML = person[TableColumns['teaching area']];
    _('refSender').innerHTML = person[TableColumns['claimed area']];

    // find area number
    const areas = getCookieJSON('missionAreasList') || [];
    for (let i = 0; i < areas.length; i++) {
        if (areas[i][0] == person[TableColumns['teaching area']] && areas[i][1] != '') {
            _('contactAreaCard').style.display = '';
            _('telnumber').href = 'tel:+' + areas[i][1];
            _('smsnumber').href = 'sms:+' + areas[i][1];
            break;
        }
    }
}
function prettyPrintRefOrigin(x) {
    switch (x.toLowerCase()) {
        case 'fb':
            return 'Facebook';
        case 'web':
            return 'Mission Website';
        case 'wix':
            return 'Mission Website';
        case 'ig':
            return 'Instagram';
        default:
            return x;
    }
}
async function fillMessageExamples(folderName, pasteBox) {
    let areaEmail = getCookie('areaUserEmail') || null;
    const person = idToReferral(getCookieJSON('linkPages'));
    let requestType = person[TableColumns['type']];
    const emailLink = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[TableColumns['email']] + '&entry.873933093=' + areaEmail + '&entry.1947536680=';
    const link_beginning = (folderName == 'sms') ? ('sms:' + encodeURI(String(person[TableColumns['phone']])) + '?body=') : emailLink;
    const _destination = (folderName == 'sms') ? '_parent' : '_blank';
    _('startBlankBtn').href = link_beginning;
    _('startBlankBtn').target = _destination;
    const reqMssgUrl = 'templates/' + folderName + '/' + encodeURI(requestType) + '.txt';
    //console.log(reqMssgUrl);
    const rawFetch = await safeFetch(reqMssgUrl);
    const rawTxt = await rawFetch.text();

    const Messages = rawTxt.split(/\n{4,}/gm);
    //console.log(Messages);
    let output = "";
    for (let i = 0; i < Messages.length; i++) {
        if (Messages[i].match(/{[^}]*}/gm) == null) {
            const this_url = link_beginning + encodeURI(Messages[i]);
            output += '<div class="w3-panel w3-card-subtle w3-light-gray w3-padding-16"><div class="googleMessage">' + Messages[i] + '</div><a href="' + this_url + '"><div class="useThisTemplateBtn">Use This Template</div></a></div>'
        } else {
            output += '<div class="w3-panel w3-card-subtle w3-light-gray w3-padding-16"><div class="googleMessage">' + Messages[i] + '</div><button onclick="sendToCompletionPage(\'' + folderName + '\', this)" class="useThisTemplateBtn">Use This Template</button></div>';
        }
    }
    pasteBox.innerHTML = output;
}
function sendToCompletionPage(smsOrEmail, el) {
    setCookie('completeThisMessage', el.previousElementSibling.innerHTML);
    safeRedirect(smsOrEmail + '_completer.html');
}
function setDebugMode(yesNo) {
    localStorage.setItem('debug-mode', String(yesNo));
    window.location.reload();
}
function syncPageFillIn() {
    if (DEBUG_MODE) {
        _('debug-mode-switch').setAttribute('checked', '');
    }
    let syncDate = new Date(data.area_specific_data.last_sync);
    _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate.toLocaleString() + '</div>';
}
function FORCEsyncPageFillIn() {
    let syncDate = '';
    if (data != null) {
        syncDate = new Date(data.area_specific_data.last_sync).toLocaleString();
    }
    _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate + '</div>';
}
function syncButton(el) {
    SYNC().then(() => {
        safeRedirect(el.getAttribute('href'));
    });
}
function sendToAnotherArea() {
    let person = idToReferral(getCookieJSON('linkPages'));
    if (person == null) {
        JSAlert.alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const newArea = document.getElementById('areadropdown').value;

    // set new area in data and save to cookie
    person[TableColumns['sent status']] = 'Sent';
    person[TableColumns['teaching area']] = newArea;

    //givePoints
    setAddFoxPoints(10);
    setCookieJSON('dataSync', data);

    // follow up
    let nextFU = new Date();
    person[TableColumns['sent date']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

    nextFU.setDate(nextFU.getDate() + CONFIG['follow ups']['initial delay after sent']);
    nextFU.setHours(3, 0, 0, 0);
    person[TableColumns['next follow up']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

    idToReferral(getCookieJSON('linkPages')) = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}

function fillInDeceeaseReasons(el) {
    let out = "<option></option>";
    for (let i = 0; i < Object.keys(CONFIG['decease reasons']).length; i++) {
        out += '<option value="' + CONFIG['decease reasons'][Object.keys(CONFIG['decease reasons'])[i]] + '">' + Object.keys(CONFIG['decease reasons'])[i] + '</option>';
    }
    el.innerHTML = out;
}
function fillInFollowUpOptions(el) {
    let out = "<option></option>";
    for (let i = 0; i < Object.keys(CONFIG['follow ups']['status delays']).length; i++) {
        out += '<option value="' + i + '">' + Object.keys(CONFIG['follow ups']['status delays'])[i] + '</option>';
    }
    el.innerHTML = out;
}
function saveFollowUpForm() {
    let person = data.overall_data.follow_ups[getCookieJSON('linkPages')];
    if (person == null) {
        JSAlert.alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const status = document.getElementById('statusdropdown').value;

    //givePoints
    setAddFoxPoints(3);
    setCookieJSON('dataSync', data);

    let clickedOption = Object.keys(CONFIG['follow ups']['status delays'])[parseInt(status)];
    let delay = CONFIG['follow ups']['status delays'][clickedOption];

    if (typeof delay === 'string' || delay instanceof String) {
        person[TableColumns['AB status']] = delay;
        person[TableColumns['next follow up']] = null;
    } else {
        let nextFU = new Date();
        nextFU.setDate(nextFU.getDate() + delay);
        nextFU.setHours(3, 0, 0, 0);
        person[TableColumns['next follow up']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');
    }

    person[TableColumns['follow up status']] = status;
    person[TableColumns['amount of times followed up']] = parseInt(person[TableColumns['amount of times followed up']]) + 1;

    data.overall_data.follow_ups[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);

    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function hasPersonBeenContactedToday(per) {
    try {
        let todaysI = getTodaysInxdexOfAttempts(per);
        if (per[ TableColumns['attempt log'] ] == '') { return false }
        let log = JSON.parse(per[ TableColumns['attempt log'] ])[todaysI];
        return !(log[0]==0 && log[1]==0 && log[2]==0);
    } catch (e) {}
    return true;
}
function getTodaysInxdexOfAttempts(per) {
    let sentDate = new Date(per[ TableColumns['date'] ]);
    sentDate.setHours(0,0,0,0);
    return Math.floor((new Date() - sentDate) / (1000 * 60 * 60 * 24));
}
async function clearTodaysAttempts() {
    let person = idToReferral(getCookieJSON('linkPages'));
    let x = getTodaysInxdexOfAttempts(person);
    let al = JSON.parse(person[TableColumns['attempt log']]);
    al[x][0] = 0;
    al[x][1] = 0;
    al[x][2] = 0;
    person[TableColumns['attempt log']] = JSON.stringify(al);
    try {
        _('attemptLogDot_0,'+x).classList.remove('contactDotBeenAttempted');
        _('attemptLogDot_1,'+x).classList.remove('contactDotBeenAttempted');
        _('attemptLogDot_2,'+x).classList.remove('contactDotBeenAttempted');
    } catch (e) {}
    // save this change
    await savePerson(person);
}
function logAttemptBeforeSendingToLink(el, type) {
    logAttempt(type);
    setTimeout(() => {
        safeRedirect('contact_info.html');
    }, 10);
    localStorage.setItem('justAttemptedContact', '1');
}
async function logAttempt(y) {
    let person = idToReferral(getCookieJSON('linkPages'));
    let x = getTodaysInxdexOfAttempts(person);
    console.log(x);
    let al = [[0, 0, 0],[0, 0, 0],[0, 0, 0],[0, 0, 0],[0, 0, 0],[0, 0, 0],[0, 0, 0]];
    try {
        al = JSON.parse(person[TableColumns['attempt log']]);
    } catch(e) {}
    if (x > al.length) {
        return false; // person is older than the amount of days in attempt log
    }
    al[x][y] = 1;
    person[TableColumns['attempt log']] = JSON.stringify(al);

    try {
        _('attemptLogDot_'+y+','+x).classList.add('contactDotBeenAttempted');
    } catch (e) {}
    // save this change
    savePerson(person);
}
function fillInAttemptLog() {
    let person = idToReferral(getCookieJSON('linkPages'));
    let al = Array(7).fill([0, 0, 0]);
    try {
        al = JSON.parse(person[TableColumns['attempt log']]);
    } catch (e) {
        person[TableColumns['attempt log']] = JSON.stringify(al);
    }

    // make days of the week start on right day
    let startDay = new Date(person[TableColumns['date']]);
    const shorterDays = ['sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon'];
    let daysString = '<td></td>';
    for (let i = 0; i < 7; i++) {
        daysString += '<td>' + shorterDays[startDay.getDay() + i] + '</td>';
    }
    _('attemptLog_weekdays').innerHTML = daysString;

    //set dot colors
    for (let i = 0; i < al.length; i++) {
        for (let j = 0; j < al[i].length; j++) {
            if (al[i][j] == 1) {
                _('attemptLogDot_' + j + ',' + i).classList.add('contactDotBeenAttempted');
            }
        }
    }

    //highlight todays thing
    try {
        let todaysI = getTodaysInxdexOfAttempts(person);
        _('attemptLog_dayIndex' + todaysI).style.backgroundColor = 'var(--light-grey)';
        for (let i = 0; i < 7; i++) {
            _('attemptLogDot_0,' + i).disabled = (i!=todaysI);
            _('attemptLogDot_1,' + i).disabled = (i!=todaysI);
            _('attemptLogDot_2,' + i).disabled = (i!=todaysI);
        }
    } catch (e) {}
}
function sendToDeceasePage(el) {
    safeRedirect(el.getAttribute('href'));
}
function confirmDeceasePerson() {
    let customAlert = new JSAlert("Are you sure you want to decease this person? This cannot be undone");
    customAlert.addButton("Yes").then(function() {
        deceasePerson();
    });
    customAlert.addButton("No");
    customAlert.show();
}
function deceasePerson() {
    let person = idToReferral(getCookieJSON('linkPages'));
    if (person == null) {
        JSAlert.alert('something went wrong. Try again');
        safeRedirect('index.html');
    }

    // set new area in data and save to cookie
    person[TableColumns['sent status']] = 'Not interested';
    person[TableColumns['not interested reason']] = _('deceaseDropdown').value;

    // add this person's number to list of pranked numbers
    if (_('deceaseDropdown').value.includes('prank')) {
        if (!data.hasOwnProperty("new_pranked_numbers")) {
            data.new_pranked_numbers = Array();
        }
        data.new_pranked_numbers.push( [ person[TableColumns['phone']] , new Date().toISOString().split('T')[0] ] );
    }

    idToReferral(getCookieJSON('linkPages')) = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function claimPerson() {
    let person = data.overall_data.new_referrals[getCookieJSON('linkPages')];
    if (person == null) {
        JSAlert.alert('something went wrong. Try again');
        safeRedirect('index.html');
        return;
    }

    // give points
    let pnts = 5;
    let timeDiff = secondsSinceDate(new Date(person[ TableColumns['date'] ])) / 60;
    if (timeDiff < 1) {
        pnts = 40;
    } else if (timeDiff < 3) {
        pnts = 20;
    } else if (timeDiff < 5) {
        pnts = 10;
    } else if (timeDiff < 10) {
        pnts = 7;
    } else if (timeDiff < 20) {
        pnts = 6;
    }
    setAddFoxPoints(pnts);
    setCookieJSON('dataSync', data);

    person[TableColumns['claimed area']] = area;
    data.overall_data.new_referrals[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function doubleCheckSignOut(el) {
    setCookie('nextRedirectLoc', el.getAttribute('href'));
    let customAlert = new JSAlert("Are you sure you want to log out? All unsynced changes will be lost");
    customAlert.addButton("Yes").then(function() {
        safeRedirect(getCookie('nextRedirectLoc'));
    });
    customAlert.addButton("No");
    customAlert.show();
}
function timeSince_formatted(date) {
    var seconds = Math.floor((new Date() - date) / 1000);
    var interval = seconds / 31536000;
    let color = 'var(--all-good-green)';
    let timeStr = Math.floor(seconds) + " seconds";
    let found = false;
    if (interval > 1 && !found) {
        found = true;
        timeStr = Math.round(interval) + " years";
        color = 'var(--warning-red)';
    }
    interval = seconds / 2592000;
    if (interval > 1 && !found) {
        found = true;
        timeStr = Math.round(interval) + " months";
        color = 'var(--warning-red)';
    }
    interval = seconds / 86400;
    if (interval > 1 && !found) {
        found = true;
        timeStr = Math.round(interval) + " days";
        if (interval > 10.0) {
            color = 'var(--warning-red)';
        } else if (interval < 4.0) {
            color = 'var(--all-good-green)';
        } else {
            color = 'var(--warning-orange)';
        }
    }
    interval = seconds / 3600;
    if (interval > 1 && !found) {
        found = true;
        timeStr = Math.round(interval) + " hours";
        color = 'var(--all-good-green)';
    }
    interval = seconds / 60;
    if (interval > 1 && !found) {
        found = true;
        timeStr = Math.round(interval) + " minutes";
        color = 'var(--all-good-green)';
    }
    return '<a style="color:' + color + '"><i class="fa fa-info-circle"></i> ' + timeStr + '</a>';
}
/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
window.addEventListener("load", (e) => {
    try {
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0 || su_refs.length > 0) ? 'block' : 'none';
    } catch (e) { }
    try {
        _('followup_reddot').style.display = (data.overall_data.follow_ups.length > 0) ? 'block' : 'none';
    } catch (e) { }
    if (DEBUG_MODE) {
        document.body.innerHTML += `<div id="debug-table"></div>`;
    }
    if (FoxEnabled) {
        setupInboxFox();
        handleDailyAndShiftlyNotifications();
    }
});