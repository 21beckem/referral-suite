function inIframe() { try {return window.self !== window.top;} catch(e) {return true;} }
HTMLCollection.prototype.forEach = function(x) {
    return Array.from(this).forEach(x);
}
function verifySentInSMOEsAB(el) {
    const yesno = confirm("Have you already sent this person in the SMOEs Area Book? 👀");
    if (yesno) {
        safeRedirect(el.getAttribute('href'));
    }
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
            //alert('has target');
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
    } catch (e) {}
    return out;
}
function setCookieJSON(x, y) {
    return setCookie(x, JSON.stringify(y));
}
function saveBeforeInfoPage(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
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
const FoxEnabled = (document.currentScript.getAttribute('no-fox')==null && CONFIG!=null && CONFIG['InboxFox']['enable']);

if (( area==null || CONFIG==null) && document.currentScript.getAttribute('dont-redirect')==null) {
    safeRedirect('login.html');
} else {
    if (sessionStorage.getItem("logged_in")==null && document.currentScript.getAttribute('dont-redirect')==null) {
        safeRedirect('pin-code.html');
    }
}


async function SYNC(loadingCover=true) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (loadingCover) {
        _('loadingcover').style.display = '';
    }

    await SYNC_getConfig();

    moveAllChangedPeopleToASeparateAreaOfData();

    let rs_wait = SYNC_referralSuiteStuff();
    let sm_wait = SYNC_sheetMapStuff();
    let al_wait = SYNC_getMissionAreasList();

    await rs_wait;
    await sm_wait;
    await al_wait;

    await SYNC_setCurrentInboxingArea();

    saveUnchangedSyncData();

    //take away overlay
    if (loadingCover) {
        _('loadingcover').style.display = 'none';
    }
}
function saveUnchangedSyncData() {
    setCookieJSON('unchangedSyncData', getCookieJSON('dataSync'));
}
function moveAllChangedPeopleToASeparateAreaOfData() {
    const unchangedSyncData = getCookieJSON('unchangedSyncData');
    if (!data.hasOwnProperty('changed_people')) {
        data.changed_people = Array();
    }
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const rockPerson = unchangedSyncData.area_specific_data.my_referrals[i];
        const changedPerson = data.area_specific_data.my_referrals[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) === JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }
    for (let i = 0; i < data.overall_data.follow_ups.length; i++) {
        const rockPerson = unchangedSyncData.overall_data.follow_ups[i];
        const changedPerson = data.overall_data.follow_ups[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) === JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }
    for (let i = 0; i < data.overall_data.new_referrals.length; i++) {
        const rockPerson = unchangedSyncData.overall_data.new_referrals[i];
        const changedPerson = data.overall_data.new_referrals[i];
        //check if person changed at all
        if (JSON.stringify(rockPerson) === JSON.stringify(changedPerson)) {
            data.changed_people.push(changedPerson);
        }
    }
    setCookieJSON('dataSync', data);
}
async function SYNC_getMissionAreasList() {
    return await safeFetch('mission_areas_list.txt')
        .then((response) => response.text())
        .then((txt) => {
            setCookieJSON('missionAreasList', txt.split('\n'));
        });
}
async function SYNC_getConfig() {
    return await safeFetch('config.json')
        .then((response) => response.json())
        .then((json) => {
            setCookieJSON('CONFIG', json);
            if (area != null) {
                setCookie('areaUserEmail', json['inboxers'][area][0]);
                leaders = (json['inboxers'][area].length > 1 && json['inboxers'][area][1].toLowerCase().includes('leader')) ? "1" : "0";
                setCookie('areaIsLeaders', leaders);
            }
            return json;
        });
}
async function SYNC_referralSuiteStuff() {
    if (data != null) {
        delete data.overall_data;
    }
    if (_CONFIG()['overall settings']['table type'].toLowerCase().includes('sql')) {
        await sortOfSYNC_UseSQL();
    } else {
        await sortOfSYNC_QueryMyself();
    }
    console.log('done: SYNC_referralSuiteStuff');
}
async function sortOfSYNC_QueryMyself() {
    const rawLink = _CONFIG()['overall settings']['table Query link'];
    let qURL = rawLink.substr(0, rawLink.lastIndexOf("/"));
    let tabId = new URLSearchParams(new URL(rawLink).hash.replace('#','?')).get('gid');
    let sURL = _CONFIG()['overall settings']['table scribe link'];
    sURL += '?area=' + area;
    sURL += '&tabId=' + tabId;
    sURL += '&searchCol=' + _CONFIG()['tableColumns']['id'];
    sURL += '&data=' + encodeURIComponent( JSON.stringify(data) );

    if (data != null && "changed_people" in data) {
        if (data.changed_people.length > 0) {
            console.log('scribe activated', sURL);
            await safeFetch(sURL);
        }
    }


    let newSyncData = {
        "overall_data" : {
            "new_referrals" : [],
            "follow_ups" : []
        },
        "area_specific_data" : {
            "my_referrals" : [],
            "last_sync" : new Date()
        }
    }
    let claimedCol = GoogleColumnToLetter(_CONFIG()['tableColumns']['claimed area'] + 1);
    let sentStatusCol = GoogleColumnToLetter(_CONFIG()['tableColumns']['sent status'] + 1);

    // read unclaimed
    let newRefs_wait = G_Sheets_Query(qURL, tabId, "select * where "+claimedCol+" = 'Unclaimed'");
    
    // read for this area
    let myFers_wait = G_Sheets_Query(qURL, tabId, "select * where "+claimedCol+" = '"+area+"' AND "+sentStatusCol+" = 'Not sent'");
    
    if (_CONFIG()['overall settings']['enable follow ups']) {
        
        // read ALL follow ups
        let nxtFU_Col = GoogleColumnToLetter(_CONFIG()['tableColumns']['next follow up'] + 1);
        let FUs = await G_Sheets_Query(qURL, tabId, "select * where "+nxtFU_Col+" < now() and "+nxtFU_Col+" is not null");
        
        // filter through follow ups. Keep those that don't have a team anymore to the first leader in the list
        for (let i = 0; i < FUs.length; i++) {
            const per = FUs[i];
            let per_claimed = per[ _CONFIG()['tableColumns']['claimed area'] ];
            if (per_claimed == area) {
                newSyncData.overall_data.follow_ups.push(per);
                continue;
            }
            let areaIsITLs = (_CONFIG()['inboxers'][area].length > 1 && _CONFIG()['inboxers'][area][1].toLowerCase().includes('leader'));
            if ( !Object.keys(_CONFIG()['inboxers']).includes(per_claimed) && areaIsITLs) {
                newSyncData.overall_data.follow_ups.push(per);
                continue;
            }
        }
    }
    // wait for all fetches to finish
    newSyncData.overall_data.new_referrals = await newRefs_wait;
    newSyncData.area_specific_data.my_referrals = await myFers_wait;

    setCookieJSON('dataSync', newSyncData);
}
function GoogleColumnToLetter(column) {
    var temp, letter = '';
    while (column > 0)
    {
      temp = (column - 1) % 26;
      letter = String.fromCharCode(temp + 65) + letter;
      column = (column - temp - 1) / 26;
    }
    return letter;
}
async function G_Sheets_Query(mainLink, tabId, query) {
    let qLink = mainLink + '/gviz/tq?tq=' + encodeURIComponent(query) + '&gid=' + tabId;
    console.log(qLink);
    return await safeFetch(qLink)
    .then((response) => response.text())
    .then((txt) => {
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
    fetchURL += (data == null) ? '' : '&data=' + encodeURIComponent( JSON.stringify(data) );
    
    console.log('SQL Fetch:', fetchURL);
    console.log('Payload:', JSON.stringify(data));
    const response = await safeFetch(fetchURL);
    let syncRes = await response.json();
    //alert('done');

    // sort through which follow ups we should have
    let newFUs = Array();
    for (let i = 0; i < syncRes.overall_data.follow_ups.length; i++) {
        const per = syncRes.overall_data.follow_ups[i];
        let per_claimed = per[ _CONFIG()['tableColumns']['claimed area'] ];
        if (per_claimed == area) {
            newFUs.push(per);
            continue;
        }
        let areaIsITLs = (_CONFIG()['inboxers'][area].length > 1 && _CONFIG()['inboxers'][area][1].toLowerCase().includes('leader'));
        if ( !Object.keys(_CONFIG()['inboxers']).includes(per_claimed) && areaIsITLs) {
            newFUs.push(per);
            continue;
        }
    }
    syncRes.overall_data.follow_ups = newFUs;
    
    console.log(syncRes);
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
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
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
    const time_beginning = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate();
    for (let i = 0; i < dagensSchedule.length; i++) {
        const dateFrom = new Date( time_beginning + ' ' + scheduleTimes[i][0].trim() + ':00.000' );
        const dateTo = new Date( time_beginning + ' ' + scheduleTimes[i][1].trim() + ':00.000' );
        if ( d.getTime() <= dateTo.getTime() && d.getTime() >= dateFrom.getTime() ) {
            return dagensSchedule[i];
        }
    }
    return '';
}
async function SYNC_setCurrentInboxingArea() {
    let thisArea = getCurrentInboxingArea();

    let areaEmail = _CONFIG()['inboxers'][0];
    const reqUrl = _CONFIG()['overall settings']['table scribe link'] + '?currentInboxer=' + encodeURI(thisArea) + '&email=' + encodeURI(areaEmail);
    await safeFetch( reqUrl );
}
function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        let dotStyle = `<div class="w3-bar-item w3-circle">
            <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
        </div>`;
        if (per[ CONFIG['tableColumns']['type'] ].toLowerCase().includes('family history')) {
            dotStyle = `<div class="w3-bar-item w3-circle">
                <div class="w3-left-align w3-large w3-text-green" style="width:20px;height:20px; margin-top: 27px;"><b>FH</b></div>
            </div>`;
        }
        const elapsedTime = timeSince_formatted(new Date(per[ CONFIG['tableColumns']['date'] ]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="claim_the_referral.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">` + dotStyle + `
            <div class="w3-bar-item">
              <span class="w3-large">` + per[ CONFIG['tableColumns']['first name'] ] + ' ' + per[ CONFIG['tableColumns']['last name'] ] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ') + `</span>
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
            <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
        </div>`;
        let nextPage = 'contact_info.html';
        if (per[ CONFIG['tableColumns']['type'] ].toLowerCase().includes('family history')) {
            dotStyle = `<div class="w3-bar-item w3-circle">
                <div class="w3-left-align w3-large w3-text-green" style="width:20px;height:20px; margin-top: 27px;"><b>FH</b></div>
            </div>`;
            nextPage = 'fh_referral_info.html';
        }
        const elapsedTime = timeSince_formatted(new Date(per[ CONFIG['tableColumns']['date'] ]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="` + nextPage + `" class="person-to-click">
          <div class="w3-bar" style="display: flex;">` + dotStyle + `
            <div class="w3-bar-item">
              <span class="w3-large">` + per[ CONFIG['tableColumns']['first name'] ] + ' ' + per[ CONFIG['tableColumns']['last name'] ] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ') + `</span>
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
        const elapsedTime = timeSince_formatted(new Date(per[ CONFIG['tableColumns']['next follow up'] ]));
        output += `<aa onclick="saveBeforeInfoPage(` + i + `, this)" href="follow_up_on.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 27px;">
                <i class="fa fa-calendar-check-o" style="color:#1d53b7; font-size:22px"></i>
              </div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[ CONFIG['tableColumns']['first name'] ] + ' ' + per[ CONFIG['tableColumns']['last name'] ] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ') + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('yourfollowups').innerHTML = output;
}
function fillInFHInfo() {
    const person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    _('contactname').innerHTML = person[ CONFIG['tableColumns']['first name'] ] + ' ' + person[ CONFIG['tableColumns']['last name'] ];
    _('personName').innerHTML = _('contactname').innerHTML;
    _('email').innerHTML = person[ CONFIG['tableColumns']['email'] ];
    _('address').innerHTML = person[ CONFIG['tableColumns']['city'] ] + ' ' + person[ CONFIG['tableColumns']['zip'] ];
    _('FH_lang').innerHTML = CONFIG['overall settings']['most common language in mission'];
    _('FH_message').innerHTML = makeFHMessage(person);
}
function makeFHMessage(per) {
    if (per[ CONFIG['tableColumns']['referral origin'] ].toLowerCase().includes('fb') || per[ CONFIG['tableColumns']['referral origin'] ].toLowerCase().includes('ig')) {
return `This is a SLÄKT UPPTÄCKT REFERRAL!! This person clicked on a FB ad and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[ CONFIG['tableColumns']['help request'] ] + `

How experienced they are: ` + per[ CONFIG['tableColumns']['experience'] ];
    } else {
        return `This is a VANDRAITRO REFERRAL!! This person went to the website and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[ CONFIG['tableColumns']['help request'] ] + `

How experienced they are: ` + per[ CONFIG['tableColumns']['experience'] ];
    }
}
function fillInContactInfo() {
    const person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    _('contactname').innerHTML = person[ CONFIG['tableColumns']['full name'] ];
    //_('telnumber').href = 'tel:+' + person[ CONFIG['tableColumns']['phone'] ];
    //_('smsnumber').href = 'sms:+' + person[ CONFIG['tableColumns']['phone'] ];
    //_('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';

    _('referraltype').innerHTML = person[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ');
    _('referralorigin').innerHTML = prettyPrintRefOrigin(person[ CONFIG['tableColumns']['referral origin'] ]);
    _('phonenumber').innerHTML = person[ CONFIG['tableColumns']['phone'] ];
    _('email').innerHTML = person[ CONFIG['tableColumns']['email'] ];
    let addStr = person[ CONFIG['tableColumns']['street address'] ] + ' ' + person[ CONFIG['tableColumns']['city'] ] + ' ' + person[ CONFIG['tableColumns']['zip'] ];
    _('address').innerHTML = addStr;
    _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
    _('adName').innerHTML = person[ CONFIG['tableColumns']['ad name'] ];
    _('adDeck').href = CONFIG['home page links']['ad deck'];
    _('prefSprak').innerHTML = (person[ CONFIG['tableColumns']['lang'] ] == "") ? "Undeclared" : person[ CONFIG['tableColumns']['lang'] ];
    fillInAttemptLog();
}
function openGoogleSlides(link) {
    setCookie('openThisSlides', link);
    safeRedirect('view_google_slides.html');
}
function setHomeBigBtnLink(elId) {
    let link = CONFIG['home page links'][elId];
    const el = _(elId);
    if (link.includes('www.canva.com')) {
        link = link.substr(0, link.lastIndexOf("/")) + '/view?embed';
        el.setAttribute('onclick', "openGoogleSlides('"+link+"')");
    } else if (link.includes('docs.google.com')) {
        link = link.substr(0, link.lastIndexOf("/")) + '/embed';
        el.setAttribute('onclick', "openGoogleSlides('"+link+"')");
    } else {
        console.log('Unrecognized presentation link. Will open in new tab:' + link);
        el.href = link.replace("{Area}", area);
        el.setAttribute('target', '_blank');
    }
}
function callThenGoBack() {
    const person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    window.open('tel:+' + person[ CONFIG['tableColumns']['phone'] ],'_blank');
    safeRedirect('contact_info.html');
}
function fillInHelpBeforeCallPage() {
    const person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    let thisUrl = CONFIG['tips before calling'][ person[ CONFIG['tableColumns']['type'] ] ];
    thisUrl = thisUrl.substr(0, thisUrl.lastIndexOf("/")) + '/embed';
    _('google_slides_import').src = thisUrl;
}
function fillInFollowUpInfo() {
    const person = data.overall_data.follow_ups[getCookieJSON('linkPages')];
    _('contactname').innerHTML = person[ CONFIG['tableColumns']['full name'] ];
    _('referraltype').innerHTML = person[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ');
    _('lastAtt').innerHTML = new Date(person[ CONFIG['tableColumns']['sent date'] ]).toLocaleDateString("en-US", {weekday:'long',year:'numeric',month:'long',day:'numeric'});
    let fuTimes = person[ CONFIG['tableColumns']['amount of times followed up'] ];
    _('followUpCount').innerHTML = fuTimes + ((parseInt(fuTimes)==1) ? ' time' : ' times');
    _('refLoc').innerHTML = person[ CONFIG['tableColumns']['teaching area'] ];
    _('refLoc2').innerHTML = person[ CONFIG['tableColumns']['teaching area'] ];
    _('refSender').innerHTML = person[ CONFIG['tableColumns']['claimed area'] ];
}
function prettyPrintRefOrigin(x) {
    switch (x.toLowerCase()) {
        case 'fb':
            return 'Facebook';
        case 'web':
            return 'VandraITro.se';
        case 'wix':
            return 'VandraITro.se';
        case 'ig':
            return 'Instagram';
        default:
            return x;
    }
}
async function fillMessageExamples(requestType, folderName, pasteBox) {
    let areaEmail = getCookie('areaUserEmail') || null;
    if (areaEmail == null) {
        await safeFetch('login.html').then(res => res.text()).then(txt => {
            const matches = txt.matchAll(/\<button(.*)email=\"(.*)\"(.*)\>(.*)<\/button>/gmi);
            for (const match of matches) {
                if (match[4] == area) {
                    areaEmail = match[2];
                    break;
                }
            }
        });
    }
    const person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    const emailLink = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[ CONFIG['tableColumns']['email'] ] + '&entry.873933093=' + areaEmail + '&entry.1947536680=';
    const link_beginning = (folderName == 'sms') ? ('sms:' + encodeURI(String(person[ CONFIG['tableColumns']['phone'] ])) + '?body=') : emailLink;
    const _destination = (folderName == 'sms') ? '_parent' : '_blank';
    _('startBlankBtn').href = link_beginning;
    _('startBlankBtn').target = _destination;
	const reqMssgUrl = 'templates/' + folderName + '/' + encodeURI(requestType) + '.txt';
	//console.log(reqMssgUrl);
	const rawFetch = await safeFetch(reqMssgUrl);
	const rawTxt = await rawFetch.text();

	text = rawTxt;
	const Messages = text.split(/\n{4,}/gm);
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
function syncPageFillIn() {
    let syncDate = new Date(data.area_specific_data.last_sync);
    _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate.toLocaleString() + '</div>';
}
function FORCEsyncPageFillIn() {
    if (data != null) {
        let syncDate = new Date(data.area_specific_data.last_sync);
        _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate.toLocaleString() + '</div>';
    }
}
function syncButton(el) {
    SYNC().then(() => {
        safeRedirect(el.getAttribute('href'));
    });
}
function sendToAnotherArea() {
    let person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const newArea = document.getElementById('areadropdown').value;

    // set new area in data and save to cookie
    person[ CONFIG['tableColumns']['sent status'] ] = 'Sent';
    person[ CONFIG['tableColumns']['teaching area'] ] = newArea;

    // follow up
    let nextFU = new Date();
    person[ CONFIG['tableColumns']['sent date'] ] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

    nextFU.setDate(nextFU.getDate() + CONFIG['follow ups']['initial delay after sent']);
    nextFU.setHours(3,0,0,0);
    person[ CONFIG['tableColumns']['next follow up'] ] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

    data.area_specific_data.my_referrals[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}

function fillInDeceeaseReasons(el) {
    let out = "<option></option>";
    for (let i = 0; i < Object.keys(CONFIG['decease reasons']).length; i++) {
        out += '<option value="' + CONFIG['decease reasons'][ Object.keys(CONFIG['decease reasons'])[i] ] + '">' + Object.keys(CONFIG['decease reasons'])[i] + '</option>';
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
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const status = document.getElementById('statusdropdown').value;

    let clickedOption = Object.keys(CONFIG['follow ups']['status delays'])[ parseInt(status) ];
    let delay = CONFIG['follow ups']['status delays'][clickedOption];

    if (typeof delay === 'string' || delay instanceof String) {
        person[ CONFIG['tableColumns']['AB status'] ] = delay;
        person[ CONFIG['tableColumns']['next follow up'] ] = null;
    } else {
        let nextFU = new Date();
        nextFU.setDate(nextFU.getDate() + delay);
        nextFU.setHours(3,0,0,0);
        person[ CONFIG['tableColumns']['next follow up'] ] = nextFU.toISOString().slice(0, 19).replace('T', ' ');
    }
    
    person[ CONFIG['tableColumns']['follow up status'] ] = status;
    person[ CONFIG['tableColumns']['amount of times followed up'] ] = parseInt( person[ CONFIG['tableColumns']['amount of times followed up'] ] ) + 1;

    data.overall_data.follow_ups[getCookieJSON('linkPages')] = person;

    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function logAttempt(el, y, x) {
    let person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    let al = JSON.parse(person[ CONFIG['tableColumns']['attempt log'] ]);
    let nowAttempted = !(al[x][y]==1);
    al[x][y] = (nowAttempted) ? 1 : 0;
    person[ CONFIG['tableColumns']['attempt log'] ] = JSON.stringify(al);
    
    if (nowAttempted) {
        el.classList.add('contactDotBeenAttempted');
    } else {
        el.classList.remove('contactDotBeenAttempted');
    }
    // save this change
    data.area_specific_data.my_referrals[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);
}
function fillInAttemptLog() {
    let person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    let al = Array(7).fill([0,0,0]);
    try {
        al = JSON.parse(person[ CONFIG['tableColumns']['attempt log'] ]);
    } catch (e) {
        person[ CONFIG['tableColumns']['attempt log'] ] = JSON.stringify(al);
    }

    // make days of the week start on right day
    let startDay = new Date(person[ CONFIG['tableColumns']['date'] ]);
    const shorterDays = ['sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon'];
    let daysString = '<td></td>';
    for (let i = 0; i < 7; i++) {
        daysString += '<td>' + shorterDays[startDay.getDay() + i] + '</td>';
    }
    _('attemptLog_weekdays').innerHTML = daysString;

    //set dot colors
    for (let i = 0; i < al.length; i++) {
        for (let j = 0; j < al[i].length; j++) {
            if (al[i][j]==1) {
                _('attemptLogDot_'+j+','+i).classList.add('contactDotBeenAttempted');
            }
        }
    }
}
function sendToDeceasePage(el) {
    safeRedirect(el.getAttribute('href'));
}
function deceasePerson() {
    let youSure = confirm("Are you sure you want to decease this person? This cannot be undone");
    if (!youSure) {
        return;
    }
    let person = data.area_specific_data.my_referrals[getCookieJSON('linkPages')];
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }

    // set new area in data and save to cookie
    person[ CONFIG['tableColumns']['sent status'] ] = 'Not interested';
    person[ CONFIG['tableColumns']['not interested reason'] ] = _('deceaseDropdown').value;

    data.area_specific_data.my_referrals[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function claimPerson() {
    let person = data.overall_data.new_referrals[getCookieJSON('linkPages')];
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
        return;
    }
    
    person[ CONFIG['tableColumns']['claimed area'] ] = area;
    data.overall_data.new_referrals[getCookieJSON('linkPages')] = person;
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function doubleCheckSignOut(el) {
    let youSure = confirm("Are you sure you want to log out? All unsynced changes will be lost");
    if (!youSure) {
        return;
    }
    safeRedirect(el.getAttribute('href'));
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
function setupInboxFox() {
    window.InboxFox = new WebPal();
    InboxFox.pokeFunction = () => {
        InboxFox.ask('Hey! Need any help?', ['Yes Please!', 'No Thanks :)'], (choice) => {
            if (choice.includes('No')) {
                InboxFox.say('Okay, just let me know :)');
            } else {
                location.href = 'https://www.google.com/search?q=i+need+help';
            }
        }, true);
    }
}
/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
window.addEventListener("load", (e) => {
    try {
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0 || su_refs.length > 0) ? 'block' : 'none';
    } catch(e) {}
    try {
        _('followup_reddot').style.display = (data.overall_data.follow_ups.length > 0) ? 'block' : 'none';
    } catch(e) {}
    if (FoxEnabled) {
        setupInboxFox();
    }
});
