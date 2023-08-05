let FOX_CONFIG = getCookieJSON('FOX_CONFIG') || null;
let localFox = getCookieJSON('localFox') || {
    "yesterdaysVerse" : 0,
    "todaysDate" : new Date(),
    "dailyPointsReceived" : [],
    "lastNotificationGiven" : new Date('2002-10-22'),
    "notificationsGivenToday" : []
}


function howFarThroughShift() {
    SheetMap.load();
    const todaysShiftIs = GetTodaysSchedule().indexOfAll(area);
    if (todaysShiftIs.length == 0) {
        return null;
    }
    let lastAfterNum;
    const d = new Date();
    const dTime = d.getTime();
    const time_beginning = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    for (let i = 0; i < todaysShiftIs.length; i++) {
        const todaysShiftI = todaysShiftIs[i];

        const todaysShiftTimes = SheetMap.vars.tableDataNOW.map(x => [x[0], x[1]])[todaysShiftI];
        const dateFrom = new Date(time_beginning + ' ' + todaysShiftTimes[0].trim() + ':00.000').getTime();
        const dateTo = new Date(time_beginning + ' ' + todaysShiftTimes[1].trim() + ':00.000').getTime();
        //console.table([todaysShiftTimes, todaysShiftI, dTime, dateFrom, dateTo]);
        
        if (dTime < dateFrom) {
            return Math.floor(((dTime - dateFrom)/1000)/60); // minutes before shift starts
        } else if (dTime > dateTo) {
            lastAfterNum = Math.floor(((dTime - dateTo)/1000)/60); // minutes after shift ended
        } else {
            // it must be during at this point. return percentage of how far we are through the shift
            return ((dTime - dateFrom) / (dateTo - dateFrom));
        }
    
    }
    return lastAfterNum;
}
function randomFoxSayingOnTopic(thisTopic) {
    let i = Math.floor(Math.random() * FOX_CONFIG.sayings[thisTopic].length);
    return FOX_CONFIG.sayings[thisTopic][i];
}
function didIJustContactEveryoneINeedToForToday() {
    let yes = true;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const per = data.area_specific_data.my_referrals[i];
        if (!hasPersonBeenContactedToday(per)) {
            yes = false;
            break;
        }
    }
    if (!yes) {
        if (dateIsToday(new Date( data.fox.streak[data.fox.streak.length-1] ))) {
            data.fox.streak.pop();
        }
        setCookieJSON('dataSync', data);
        console.log('streak reset to last sync data');
        return;
    }
    // check if streak already done for today
    if (dateIsToday(new Date( data.fox.streak[0] ))) {
        return;
    }
    // increase streak num and last day
    const d = new Date();
    data.fox.streak.unshift( d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate() );
    setCookieJSON('dataSync', data);
    InboxFox.playAnimation('Excited');
    InboxFox.playLargeRive('streak-maintained1.riv', 'State Machine 1');
    InboxFox.say( randomFoxSayingOnTopic('streak extended') + "<br>Go sync to save it!");
}
const dateIsToday = (someDate, dayOffset=0) => {
    if (String(someDate.getFullYear()).includes('1970')) {
        return false;
    }
    let today = new Date();
    today.setDate(today.getDate() + dayOffset);
    return someDate.getDate() == today.getDate() &&
        someDate.getMonth() == today.getMonth() &&
        someDate.getFullYear() == today.getFullYear()
}
function isMoreThanDaysOld(thisD, days) {
    let d = new Date();
    d.setDate(d.getDate() - days);
    return thisD.getTime() < d.getTime();
}
function secondsSinceDate(before) {
    return (new Date().getTime() - new Date(before).getTime()) / 1000;
}
function beenEnoughTimeSinceLastNotification() {
    return secondsSinceDate(localFox.lastNotificationGiven) > FOX_CONFIG['general']['minimum delay between notifications (sec)'];
}
function remindThisWithFox(remId, ifShouldFunction) {
    if (!beenEnoughTimeSinceLastNotification()) {
        return;
    }
    if (!FOX_CONFIG.reminders.hasOwnProperty(remId)) {
        console.error("This fox reminder: (\""+remId+"\") doesn't exist. Skipping past it");
        return;
    }
    const rem = FOX_CONFIG.reminders[remId]
    if (!rem[0]) {
        return false;
    }
    if (localFox.notificationsGivenToday.includes(remId)) {
        return false;
    }
    if (rem[1]==null || rem[2]==null) {
        return true;
    }
    if (howFarThroughShift() > rem[1] && howFarThroughShift() < rem[2]) {
        if (!window.variableToShowFoxIfFoxIsAlreadyShowingANotificationOnRefresh) {
            window.variableToShowFoxIfFoxIsAlreadyShowingANotificationOnRefresh = true;
            ifShouldFunction();
            localFox.notificationsGivenToday.push(remId);
            localFox.lastNotificationGiven = new Date();
            setCookieJSON('localFox', localFox);
        }
    }
    return true;
}
function resetFoxNotifications() {
    localFox.notificationsGivenToday = Array();
    setCookieJSON('localFox', localFox);
}
function handleDailyAndShiftlyNotifications() {
    // check todays date in local data
    const d = new Date();
    let tStr = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    if (localFox.todaysDate != tStr || howFarThroughShift() < 0) {
        localFox.todaysDate = tStr;
        localFox.notificationsGivenToday = Array();
        localFox.dailyPointsReceived = Array();
        setCookieJSON('localFox', localFox);
    }

    // remember to pray
    remindThisWithFox('begin with prayer', () => {
        InboxFox.playAnimation('Wave1');
        InboxFox.ask( randomFoxSayingOnTopic('prayer') , ['Yes!', 'Just did it!'], (choice) => {
            let scrs = getCookieJSON('fox_daily_scriptures');
            let i = 0;
            while (true) {
                i = Math.floor(Math.random() * scrs.length);
                if (i!=localFox.yesterdaysVerse) {
                    break;
                }
            }
            localFox.yesterdaysVerse = i;
            setCookieJSON('localFox', localFox);
            InboxFox.playAnimation('Read and Encourage to read');
            InboxFox.say( randomFoxSayingOnTopic('scripture') + '<p class="scriptureOfDay">' + scrs[i][1] + "</p><p>" + scrs[i][0] + "</p>");
        }, false);
    });

    // reminder to contact all claimed
    remindThisWithFox('contact all claimed', () => {
        InboxFox.playAnimation('Check your time');
        InboxFox.ask( randomFoxSayingOnTopic('contact claimed') , ['Take me there!'], (choice) => {
            safeRedirect('contact_book.html');
        }, true);
    });

    // remember to end with reporting
    remindThisWithFox('end with reporting', () => {
        InboxFox.playAnimation('Have you reported?');
        InboxFox.ask( randomFoxSayingOnTopic('report') , ['Yes!', 'Take me to it!'], (choice) => {
            if (choice.includes('Yes')) {
                InboxFox.say( randomFoxSayingOnTopic('encouragement') );
            } else {
                sendToReportingForm();
            }
        }, false);
    });

    // check if we just came back from contacting
    if (localStorage.getItem('justAttemptedContact')) {
        localStorage.removeItem('justAttemptedContact');
        didIJustContactEveryoneINeedToForToday();
    }

    // set interval for checking for new referrals
    FoxPingNF();
    window.intervalToPingNF = setInterval(FoxPingNF, FOX_CONFIG['general']['delay between checking for new referrals (sec)'] * 1000);
}
async function FoxPingNF() {
    if (!beenEnoughTimeSinceLastNotification()) {
        return;
    }
    let thruShift = howFarThroughShift();
    if (thruShift < 0 || thruShift > 1) {
        return;
    }
    let newRefs = await INSTANTSYNC_pingNF();
    if (newRefs) {
        clearInterval(window.intervalToPingNF);
        InboxFox.playAnimation('Excited');
        InboxFox.say(randomFoxSayingOnTopic('new referral'));
        localFox.lastNotificationGiven = new Date();
        setCookieJSON('localFox', localFox);
    }
}
async function SYNC_foxVars() {
    // get fox config
    await SYNC_getFoxConfig(_CONFIG()['InboxFox']['fox config']);
    await SYNC_getDailyScriptureList();
    parseStreakStatus();
}
async function SYNC_getFoxConfig(lank) {
    return await safeFetch(lank)
        .then((response) => response.json())
        .then((json) => {
            setCookieJSON('FOX_CONFIG', json);
            FOX_CONFIG = json;
            return json;
        });
}
async function SYNC_getDailyScriptureList() {
    const rawLink = FOX_CONFIG['links']['daily scripture table'];
    let thisLink = rawLink.substr(0, rawLink.lastIndexOf("/"));
    let thisSheetId = new URLSearchParams(new URL(rawLink).hash.replace('#', '?')).get('gid');
    let this_res = await G_Sheets_Query(thisLink, thisSheetId, 'SELECT *', 'A1:B');
    setCookieJSON('fox_daily_scriptures', this_res);
}
function setupInboxFox() {
    window.InboxFox = new WebPal( CONFIG['inboxers'][area]['color'] );
    InboxFox.pokeFunction = () => {
        InboxFox.playAnimation('Wave1');
        InboxFox.ask('What\s up?', ['Extend Streak!', 'Coin!'], (choice) => {
            if (choice.includes('Streak')) {
                InboxFox.playLargeRive('streak-maintained1.riv', 'State Machine 1');
            } else {
                InboxFox.playLargeRive('coin1.riv', 'State Machine 1');
            }
        }, true);
    }
}
function parseStreakStatus() {
    let thisData = getCookieJSON('dataSync');
    if (thisData.fox.streak.length == 0) {
        return;
    } else if (dateIsToday(new Date(thisData.fox.streak[0]), -1)) {
        // can extend
    } else if (isMoreThanDaysOld(new Date(thisData.fox.streak[0]), 2)) {
        // maybe lost streak, but check claimed referrals
        if (thisData.area_specific_data.my_referrals.length == 0) {
            let yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            thisData.fox.streak.unshift( yesterday.getFullYear()+'-'+(yesterday.getMonth()+1)+'-'+yesterday.getDate() );
            setCookieJSON('dataSync', thisData);
        } else {
            thisData.fox.streak = Array();
            setCookieJSON('dataSync', thisData);
        }
    }
    setCookieJSON('localFox', localFox);
}
function foxStreakExtendingStatus() {
    if (data.fox.streak.length == 0) {
        return null;
    } else if (dateIsToday(new Date(data.fox.streak[0]), -1)) {
        return 'can extend';
    } else if (isMoreThanDaysOld(new Date(data.fox.streak[0]), 2)) {
        // maybe lost streak, but check claimed referrals
        if (data.area_specific_data.my_referrals.length == 0) {
            return 'can extend';
        } else {
            return null;
        }
    }
    return 'done for today';
}

async function fillInLeaderboardPage() {
    const rawReadOnlyLink = _CONFIG()['overall settings']['readonly data sheet link'];
    let readonlyLink = rawReadOnlyLink.substr(0, rawReadOnlyLink.lastIndexOf("/"));
    let readOnlyId = new URLSearchParams(new URL(rawReadOnlyLink).hash.replace('#', '?')).get('gid');
    let areaFoxStat_wait = G_Sheets_Query(readonlyLink, readOnlyId, 'SELECT *', 'A3:B');

    let foxListData = await areaFoxStat_wait;
    let newFoxList = foxListData.map(x => {
        return decodeFox(x);
    });
    let masterList = foxListData.map((item,i) => [item,newFoxList[i]]);

    let sortedByStreak = [...masterList].sort((a, b) => b[1].streak.length - a[1].streak.length);
    console.log(sortedByStreak);
    let sortedByPoints = [...masterList].sort((a, b) => b[1].points - a[1].points);
    console.log(sortedByStreak);

    SheetMap.load();

    let streakOutput = '';
    for (let i = 0; i < sortedByStreak.length; i++) {
        const areaName = sortedByStreak[i][0][1];
        if (!CONFIG.inboxers.hasOwnProperty(areaName)) {
            continue;
        }
        const imgLink = 'img/fox_profile_pics/' + CONFIG['inboxers'][areaName]['color'] + '.svg';
        streakOutput += `
        <div class="leaderboardResult">
            <div><img class="areaCircle" src="` + imgLink + `"></div>
            <div class="name">` + areaName + `</div>
            <div class="amount">` + sortedByStreak[i][1].streak.length + `</div>
        </div>`;
    }
    _('streakResultsContainer').innerHTML = streakOutput;

    let pointsOutput = '';
    for (let i = 0; i < sortedByPoints.length; i++) {
        const areaName = sortedByPoints[i][0][1];
        if (!CONFIG.inboxers.hasOwnProperty(areaName)) {
            continue;
        }
        const imgLink = 'img/fox_profile_pics/' + CONFIG['inboxers'][areaName]['color'] + '.svg';
        pointsOutput += `
        <div class="leaderboardResult">
            <div><img class="areaCircle" src="` + imgLink + `"></div>
            <div class="name">` + areaName + `</div>
            <div class="amount">` + sortedByPoints[i][1].points + `</div>
        </div>`;
    }
    _('pointsResultsContainer').innerHTML = pointsOutput;

    _('loadingAnim').style.display = 'none';
}