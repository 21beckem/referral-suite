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
function verifySUhasBeenSent(el) {
    const yesno = confirm("You sure? This person will disappear from referral suite when you click 'OK'");
    if (yesno) {
        const per = getCookieJSON('linkPages') || null;
        if (per==null) {
            return;
        }
        su_done.push( [per[0], per[3]] );
        setCookieJSON('suDone', su_done);
        //alert('syncing now');
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
        if (origin.hasAttribute("target")) {
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
function saveBeforeClaimPage(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
}
function saveBeforeSUPage(person, el) {
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

if (( area==null || CONFIG==null)  && document.currentScript.getAttribute('dont-redirect')==null) {
    safeRedirect('login.html');
}


async function SYNC(loadingCover=true) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (loadingCover) {
        _('loadingcover').style.display = '';
    }

    await SYNC_getConfig();

    let rs_wait = SYNC_referralSuiteStuff();
    let su_wait = SYNC_SUStuff();
    let sm_wait = SYNC_sheetMapStuff();

    await rs_wait;
    await su_wait;
    await sm_wait;

    await SYNC_setCurrentInboxingArea();

    //take away overlay
    if (loadingCover) {
        _('loadingcover').style.display = 'none';
    }
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
}
async function sortOfSYNC_QueryMyself() {
    let fetchURL = _CONFIG()['overall settings']['table Query link'];
    fetchURL += '/gviz/tq?tq=';

    // set values with app script


    // read unclaimed


    // read for this area


    if (_CONFIG()['overall settings']['enable follow ups']) {
        
        // read ALL follow ups


        // filter through follow ups. Keep those that don't have a team anymore to the first leader in the list
    }
}
async function G_Sheets_Query(mainLink, tabId, query) {
    return await safeFetch(mainLink + '/gviz/tq?tq=' + encodeURIComponent(query) + '&gid=' + tabId)
    .then((response) => response.text())
    .then((txt) => {
        return JSON.parse(
            txt.replace("/*O_o*/\n", "") // remove JSONP wrapper
            .replace(/(google\.visualization\.Query\.setResponse\()|(\);)/gm, "") // remove JSONP wrapper
        );
    })
}
async function sortOfSYNC_UseSQL() {
    let fetchURL = _CONFIG()['overall settings']['table Query link'];
    fetchURL += '?area=' + area;
    fetchURL += (data == null) ? '' : '&data=' + encodeURIComponent( JSON.stringify(data) );
    
    console.log('Referrals Fetch:', fetchURL);
    console.log('Payload:', JSON.stringify(data));
    const response = await safeFetch(fetchURL);
    const syncRes = await response.json();
    //alert('done');
    console.log(syncRes);

    //save to cookie
    setCookieJSON('dataSync', syncRes);
}
async function SYNC_SUStuff() {
    if (!_CONFIG()['overall settings']['enable FH referrals']) {
        return;
    }
    let fetchURL = _CONFIG()['overall settings']['table Query link'] + '?area=SU';
    fetchURL += (su_done == null) ? '' : '&data=' + encodeURI( JSON.stringify(su_done) );
    console.log('SU Fetch:', fetchURL);
    const response = await safeFetch(fetchURL);
    const syncRes = await response.json();
    //alert('done');
    console.log(syncRes);

    //save to cookie
    setCookieJSON('suSync', syncRes);
}
async function SYNC_sheetMapStuff() {
    // sync schedule changes then get updated stuff:
    const ss = new SheetMap({
        url : 'https://script.google.com/macros/s/AKfycbz4QXXjeLFUPltyk0Ufl--MMyw5kR9WwyBHBABxYD6Vr4n-o-aQ3mgPRufrbBTlnVPO/exec',
        data_validation : 'E8',
        fetchStyles : true
    });
    await SheetMap.syncChanges();
    await ss.fetch('Schedule', 'C2:BI');
}
async function SYNC_getAreaEmail() {
    //also get area email
    let areaEmail = "";
    let leaders = "0";
    await safeFetch('login.html').then(res => res.text()).then(txt => {
        let r = findAreaEmailFromHTML(txt, area);
        console.log(r);
        areaEmail = r[0];
        leaders = r[1];
    });
    setCookie('areaUserEmail', areaEmail);
    setCookie('areaIsLeaders', leaders);
}
function findAreaEmailFromHTML(txt, thisArea) {
    let areaEmail = "";
    let leaders = "0";
    const matches = txt.matchAll(/\<button(.*)email=\"(.*)\"(.*)\>(.*)<\/button>/gmi);
    for (const match of matches) {
        if (match[4] == thisArea) {
            //console.log("match",match);
            areaEmail = match[2];
            if (match[3].includes('leader')) {
                leaders = "1";
            }
            break;
        }
    }
    return [areaEmail, leaders];
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

    let areaEmail = "";
    await safeFetch('login.html').then(res => res.text()).then(txt => {
        areaEmail = findAreaEmailFromHTML(txt, thisArea)[0];
    });
    const reqUrl = _CONFIG()['overall settings']['SYNC link'] + '?currentInboxer=' + encodeURI(thisArea) + '&email=' + encodeURI(areaEmail);
    await safeFetch( reqUrl );
}
function makeListSU_people() {
    const arr = su_refs;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        const elapsedTime = timeSince_formatted(new Date(per[ CONFIG['FHColumns']['date'] ]));
        output += `<aa onclick="saveBeforeSUPage(su_refs[` + i + `], this)" href="su_referral_info.html" class="person-to-click">
        <div class="w3-bar" style="display: flex;">
          <div class="w3-bar-item w3-circle">
            <div class="w3-left-align w3-large w3-text-green" style="width:20px;height:20px; margin-top: 27px;"><b>SU</b></div>
          </div>
          <div class="w3-bar-item">
            <span class="w3-large">` + per[ CONFIG['FHColumns']['first name'] ] + ' ' + per[ CONFIG['FHColumns']['last name'] ] + `</span><br>
            <span>` + elapsedTime + `</span><br>
            <span>` + prettyPrintRefOrigin(per[ CONFIG['FHColumns']['referral origin'] ]) + `</span>
          </div>
        </div>
      </aa>`;
    }
    _('su-referrals').innerHTML = output;
}
function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        const elapsedTime = timeSince_formatted(new Date(per[1]));
        output += `<aa onclick="saveBeforeClaimPage(data.overall_data.new_referrals[` + i + `], this)" href="claim_the_referral.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[2] + ' ' + per[3] + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[0].replaceAll('_', ' ') + `</span>
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
        const elapsedTime = timeSince_formatted(new Date(per[ CONFIG['tableColumns']['date'] ]));
        output += `<aa onclick="saveBeforeInfoPage(` + JSON.stringify(per).replaceAll('"', '&quot;') + `, this)" href="contact_info.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
            </div>
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
        output += `<aa onclick="saveBeforeInfoPage(` + JSON.stringify(per).replaceAll('"', '&quot;') + `, this)" href="follow_up_on.html" class="person-to-click">
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
function fillInSUInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[ CONFIG['FHColumns']['first name'] ] + ' ' + person[ CONFIG['FHColumns']['last name'] ];
    _('referralorigin').innerHTML = prettyPrintRefOrigin(person[ CONFIG['FHColumns']['referral origin'] ]);
    _('email').innerHTML = person[ CONFIG['FHColumns']['email'] ];
    _('address').innerHTML = person[ CONFIG['FHColumns']['city'] ] + ' ' + person[ CONFIG['FHColumns']['zip'] ];
    _('SU_message').innerHTML = makeSUMessage(person);
}
function makeSUMessage(per) {
    if (per[ CONFIG['FHColumns']['referral origin'] ].toLowerCase().includes('fb') || per[ CONFIG['FHColumns']['referral origin'] ].toLowerCase().includes('ig')) {
return `This is a SLÄKT UPPTÄCKT REFERRAL!! This person clicked on a FB ad and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[ CONFIG['FHColumns']['help request'] ] + `

How experienced they are: ` + per[ CONFIG['FHColumns']['experience'] ];
    } else {
        return `This is a VANDRAITRO REFERRAL!! This person went to the website and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[ CONFIG['FHColumns']['help request'] ] + `

How experienced they are: ` + per[ CONFIG['FHColumns']['experience'] ];
    }
}
function fillInContactInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[ CONFIG['tableColumns']['full name'] ];
    _('telnumber').href = 'tel:+' + person[ CONFIG['tableColumns']['phone'] ];
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
    _('prefSprak').innerHTML = (person[ CONFIG['tableColumns']['lang'] ] == "") ? "Undeclared" : person[ CONFIG['tableColumns']['lang'] ];
}
function fillInFollowUpInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[ CONFIG['tableColumns']['full name'] ];
    _('referraltype').innerHTML = person[ CONFIG['tableColumns']['type'] ].replaceAll('_', ' ');
    _('lastAtt').innerHTML = new Date(person[ CONFIG['tableColumns']['sent date'] ]).toLocaleDateString("en-US", {weekday:'long',year:'numeric',month:'long',day:'numeric'});
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
    const person = getCookieJSON('linkPages') || null;
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
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const newArea = document.getElementById('areadropdown').value;

    // set new area in data and save to cookie
    person[ CONFIG['tableColumns']['send status'] ] = 'Sent';
    person[ CONFIG['tableColumns']['teaching area'] ] = newArea;

    if (!("changed_people" in data)) {
        data.changed_people = Array();
    }
    data.changed_people.push(person);
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}

// this controls how long until the follow up pops up again based off what answer the missionary gave.
// "green" and "Not interested" tells the system to not make any more follow-up reminders
//                          0             1         2         3         4
let followUpDelay = ["Not interested", "3 days", "7 days", "14 days", "green"];


function saveFollowUpForm() {
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const status = document.getElementById('statusdropdown').value;

    // overwrite old person
    if (!data.hasOwnProperty('follow_up_update')) {
        data.follow_up_update = Array();
    }
    
    let tosend = [person[ CONFIG['tableColumns']['type'] ], person[ CONFIG['tableColumns']['id'] ], status, followUpDelay[parseInt(status)]];
    data.follow_up_update.push(tosend);
    setCookieJSON('dataSync', data);

    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function sendToDeceasePage(el) {
    safeRedirect(el.getAttribute('href'));
}
function deceasePerson() {
    let youSure = confirm("Are you sure you want to decease this person? This cannot be undone");
    if (!youSure) {
        return;
    }
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }

    // set new area in data and save to cookie
    person[ CONFIG['tableColumns']['sent status'] ] = 'Not interested';
    person[ CONFIG['tableColumns']['not interested reason'] ] = _('deceaseDropdown').value;

    if (!("changed_people" in data)) {
        data.changed_people = Array();
    }
    data.changed_people.push(person);
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function claimPerson() {
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
        return;
    }
    if ( !("claim_these" in data) ) {
        data['claim_these'] = Array();
    }
    data['claim_these'].push(person);
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
/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
window.onload = () => {
    try {
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0 || su_refs.length > 0) ? 'block' : 'none';
    } catch(e) {}
    try {
        _('followup_reddot').style.display = (data.area_specific_data.follow_ups.length > 0) ? 'block' : 'none';
    } catch(e) {}
}