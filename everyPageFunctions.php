<?php
require_once('require_area.php');
header('Content-Type: application/javascript');
?>;
const MISSIONINFO = <?php echo(json_encode( $__MISSIONINFO )) ?>;
const TEAM = <?php echo(json_encode( $__TEAM )) ?>;
const CONFIG = <?php echo(json_encode( getConfig() )) ?>;
const UNCLAIMED = <?php echo(json_encode( getUnclaimed() )) ?>;
const CLAIMED = <?php echo(json_encode( getClaimed() )) ?>;
const FOLLOW_UPS = <?php echo(json_encode( getFollowUps() )) ?>;

// ignore all errors above this line

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
function _(x) { return document.getElementById(x); }
function saveToLinkPagesThenRedirect(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
}
const TableColumns = {
    "type" : 0,
    "id" : 1,
    "date" : 2,
    "sent status" : 3,
    "claimed area" : 4,
    "teaching area" : 5,
    "AB status" : 6,
    "first name" : 7,
    "last name" : 8,
    "phone" : 9,
    "email" : 10,
    "street address" : 11,
    "city" : 12,
    "zip" : 13,
    "lang" : 14,
    "referral origin" : 15,
    "ad name" : 16,
    "next follow up" : 17,
    "follow up status" : 18,
    "amount of times followed up" : 19,
    "sent date" : 20,
    "not interested reason" : 21,
    "attempt log" : 22,
    "help request" : 23,
    "experience" : 24
}

// not so EVERYpage functions but nice to have on every page
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
function idToReferral(id) {
    return CLAIMED.filter( x => parseInt(x[TableColumns['id']])==parseInt(id))[0];
}
async function savePerson(perArr) {
    const response = await fetch('php_functions/updatePerson.php?per='+encodeURIComponent(JSON.stringify(perArr)));
    return response.text();
}
window.addEventListener("load", (e) => {
    // if (DEBUG_MODE) {
    //     document.body.innerHTML += `<div id="debug-table"></div>`;
    // }
    // if (FoxEnabled) {
    //     setupInboxFox();
    //     handleDailyAndShiftlyNotifications();
    // }
});