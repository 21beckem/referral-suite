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
        if (data.hasAttribute('su_created')) {
            data['su_created'].push( [per[0], per[7]] );
        } else {
            data['su_created'] = [[per[0], per[7]]];
        }
        setCookieJSON('dataSync', data);
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
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
/////  above functions make everything function with basic navigation between pages. No Touch!
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
let data = getCookieJSON('dataSync') || null;
let area = getCookie('areaUser') || null;
let su_data = getCookieJSON('suSync') || null;

if (area == null) {
    safeRedirect('login.html');
}


async function SYNC(loadingCover=true) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (loadingCover) {
        _('loadingcover').style.display = '';
    }
    let rs_wait = SYNC_referralSuiteStuff();
    let su_wait = SYNC_SUStuff();
    let sm_wait = SYNC_sheetMapStuff();
    let ar_wait = SYNC_getAreaEmail();

    await rs_wait;
    await su_wait;
    await sm_wait;
    await ar_wait;

    //take away overlay
    if (loadingCover) {
        _('loadingcover').style.display = 'none';
    }
}
const referralSuiteFetchURL = 'https://script.google.com/macros/s/AKfycbzY9M1UOnfZyDMH7hsubPSgjL9-Rurku_AwDclwpbCWpl5609hhb4XjAz4SwH62xg0_/exec';
async function SYNC_referralSuiteStuff() {
    let fetchURL = referralSuiteFetchURL + '?area=';
    fetchURL += area;
    fetchURL += (data == null) ? '' : '&data=' + encodeURIComponent( JSON.stringify(data) );
    console.log(fetchURL);
    const response = await fetch(fetchURL);
    const syncRes = await response.json();
    //alert('done');
    console.log(syncRes);

    //save to cookie
    setCookieJSON('dataSync', syncRes);
}
async function SYNC_SUStuff() {
    let fetchURL = referralSuiteFetchURL + '?area=SU';
    fetchURL += (su_data == null) ? '' : '&data=' + encodeURIComponent( JSON.stringify(su_data) );
    console.log(fetchURL);
    const response = await fetch(fetchURL);
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
    await ss.fetch('Schedule', 'C1:8');
}
async function SYNC_getAreaEmail() {
    //also get area email
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
    setCookie('areaUserEmail', areaEmail);
}
function makeListSU_people() {
    const arr = su_data;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        const elapsedTime = timeSince_formatted(new Date(per[0]));
        output += `<aa onclick="saveBeforeSUPage(su_data[` + i + `], this)" href="su_referral_info.html" class="person-to-click">
        <div class="w3-bar" style="display: flex;">
          <div class="w3-bar-item w3-circle">
            <div class="w3-left-align w3-large w3-text-green" style="width:20px;height:20px; margin-top: 27px;"><b>SU</b></div>
          </div>
          <div class="w3-bar-item">
            <span class="w3-large">` + per[1] + ' ' + per[2] + `</span><br>
            <span>` + elapsedTime + `</span><br>
            <span>` + prettyPrintRefOrigin(per[7]) + `</span>
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
              <span class="w3-large">` + per[2]  + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[0] + `</span>
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
        const elapsedTime = timeSince_formatted(new Date(per[1]));
        output += `<aa onclick="saveBeforeInfoPage(` + JSON.stringify(per).replaceAll('"', '&quot;') + `, this)" href="contact_info.html" class="person-to-click">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[5]  + `</span><br>
              <span>` + elapsedTime + `</span><br>
              <span>` + per[0] + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('yourreferrals').innerHTML = output;
}
function fillInSUInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[1] + ' ' + person[2];
    _('referralorigin').innerHTML = prettyPrintRefOrigin(person[7]);
    _('email').innerHTML = person[3];
    _('address').innerHTML = person[4] + ' ' + person[5];
    _('SU_message').innerHTML = makeSUMessage(person);
}
function makeSUMessage(per) {
    if (per[7].toLowerCase().includes('fb') || per[7].toLowerCase().includes('ig')) {
return `This is a SLÄKT UPPTÄCKT REFERRAL!! This person clicked on a FB ad and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[6];
    } else {
        return `This is a VANDRAITRO REFERRAL!! This person went to the website and wants help with släktforskning! Contact them as as soon as possible. USE EMAIL!

LYCKA TILL!

What they want help with: ` + per[6];
    }
}
function fillInContactInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[5];
    _('telnumber').href = 'tel:+' + person[8];
    //_('smsnumber').href = 'sms:+' + person[8];
    //_('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';

    _('referraltype').innerHTML = person[0];
    _('referralorigin').innerHTML = prettyPrintRefOrigin(person[13]);
    _('phonenumber').innerHTML = person[8];
    _('email').innerHTML = person[9];
    let addStr = person[10] + ' ' + person[11] + ' ' + person[12];
    _('address').innerHTML = addStr;
    _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
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
    const emailLink = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=' + areaEmail + '&entry.1947536680=';
    const link_beginning = (folderName == 'sms') ? ('sms:' + encodeURI(String(person[8])) + '?body=') : emailLink;
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
    let syncDate = new Date(data.area_specific_data.last_sync);
    _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate.toLocaleString() + '</div>';
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
    person[3] = 'Sent';
    person[4] = newArea;

    // overwrite old person
    let found = false;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const oldPer = data.area_specific_data.my_referrals[i];
        if (oldPer[1] == person[1]) {
            found = true;
            data.changed_people = Array();
            data.changed_people.push(person);
            setCookieJSON('dataSync', data);
            break;
        }
    }
    if (!found) {
        alert("something went wrong, we couldn't find this person. Try again");
        safeRedirect('index.html');
    }
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function deceasePerson() {
    const person = getCookieJSON('linkPages') || null;
    let youSure = confirm("Are you sure you want to decease this person? This cannot be undone");
    if (!youSure) {
        return;
    }
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }

    // set new area in data and save to cookie
    person[3] = 'Not interested';

    // overwrite old person
    let found = false;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const oldPer = data.area_specific_data.my_referrals[i];
        if (oldPer[1] == person[1]) {
            found = true;
            data.changed_people = Array();
            data.changed_people.push(person);;
            setCookieJSON('dataSync', data);
            break;
        }
    }
    if (!found) {
        alert("something went wrong, we couldn't find this person. Try again");
        safeRedirect('index.html');
    }
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
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0) ? 'block' : 'none';
    } catch(e) {}
}
