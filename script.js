function inIframe() { try {return window.self !== window.top;} catch(e) {return true;} }
function redirectAfterFunction(el) {
    alert("doing functiony stuff");
    safeRedirect(el.getAttribute('href'));
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
function _(x) { return document.getElementById(x); }
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
/////  above functions make everything function with basic navigation between pages. No Touch!
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
let data = getCookieJSON('dataSync') || null;
let area = getCookie('areaUser') || null;

if (area == null) {
    safeRedirect('login.html');
}


async function SYNC(print=true, justRead=false) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (print) {
        _('loadingcover').style.display = '';
    }
    let fetchURL = 'https://script.google.com/macros/s/AKfycbzsJCmPlOzMtlJFyiUry-lW5CPYbmC6Id_n8dET2Z1YYYjA5nDldgcDM0c-4scRGfM/exec?area=';
    fetchURL += area;
    fetchURL += (data == null || justRead) ? '' : '&data=' + encodeURIComponent( JSON.stringify(data) );
    console.log(fetchURL);
    const response = await fetch(fetchURL);
    const syncRes = await response.json();
    //alert('done');
    console.log(syncRes);

    //save to cookie
    setCookieJSON('dataSync', syncRes);

    //take away overlay
    if (print) {
        _('loadingcover').style.display = 'none';
    }
}

function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<aa onclick="saveBeforeInfoPage(data.overall_data.new_referrals[` + i + `], this)" href="claim_the_referral.html">
          <li class="w3-bar" style="display: flex">
            <div class="w3-bar-item w3-circle" style="width:65px;height:65px">
              <div class="w3-margin-top w3-left-align w3-dot w3-circle" style="width:20px;height:20px"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large w3-ellipsis">` + per[2] + `</span><br>
              <span>` + new Date(per[1]).toLocaleString() + `</span>
            </div>
          </li>
          </aa>`;
    }
    _('unclaimedlist').innerHTML = output;
}
function makeListClaimedPeople(arr) {
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<aa onclick="saveBeforeInfoPage(data.area_specific_data.my_referrals[` + i + `], this)" href="contact_info.html">
        <li class="w3-bar" style="display: flex">
          <div class="w3-bar-item w3-circle" style="width:65px;height:65px">
            <div class="w3-margin-top w3-left-align w3-dot w3-circle" style="width:20px;height:20px"></div>
          </div>
          <div class="w3-bar-item">
            <span class="w3-large w3-ellipsis">` + per[5]  + `</span><br>
            <span>` + new Date(per[1]).toLocaleString() + `</span>
          </div>
        </li>
        </aa>`;
    }
    _('yourreferrals').innerHTML = output;
}
function fillInContactInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[5];
    _('telnumber').href = 'tel:+' + person[8];
    _('smsnumber').href = 'sms:+' + person[8];
    _('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';

    _('referraltype').innerHTML = person[0];
    _('phonenumber').innerHTML = '+' + person[8];
    _('email').innerHTML = person[9];
    let addStr = person[10] + ' ' + person[11] + ' ' + person[12];
    _('address').innerHTML = addStr;
    _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
}

function syncPageFillIn() {
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
/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
window.onload = () => {
    try {
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0) ? 'block' : 'none';
    } catch(e) {}
}
