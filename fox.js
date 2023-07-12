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
    InboxFox.playAnimation('Wave1');
    InboxFox.say("Let's go!! Good job, you extended your streak!")
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
function handleDailyAndShiftlyNotifications() {
    // check if we just came back from contacting
    if (localStorage.getItem('justAttemptedContact')) {
        localStorage.removeItem('justAttemptedContact');
        didIJustContactEveryoneINeedToForToday();
    }
}
function parseStreakStatus() {
    let thisData = getCookieJSON('dataSync');
    if (thisData.fox.streak.length == 0) {
        return;
    } else if (dateIsToday(new Date(thisData.fox.streak[0]), -1)) {
        alert('You can extend your streak!');
    } else if (isMoreThanDaysOld(new Date(thisData.fox.streak[0]), 2)) {
        // maybe lost streak, but check claimed referrals
        if (thisData.area_specific_data.my_referrals.length == 0) {
            let yesterday = new Date();
            yesterday.setDate(yesterday.getDate() + 1);
            thisData.fox.streak.unshift( yesterday.getFullYear()+'-'+(yesterday.getMonth()+1)+'-'+yesterday.getDate() );
            setCookieJSON('dataSync', thisData);
            alert('Streak saved by no referrals!');
        } else {
            thisData.fox.streak = Array();
            setCookieJSON('dataSync', thisData);
            alert('STREAK LOST?!');
        }
    }
}