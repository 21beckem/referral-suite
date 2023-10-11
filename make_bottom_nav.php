<?php
require_once('require_area.php');
function make_bottom_nav($pageNum, $bottomSpacingPX='80px') {
    global $__TEAM;
?>

<div style="height: <?php echo($bottomSpacingPX); ?>;"></div>
<div id="BottomNavBar">
    <div class="bottomNavBtnParent <?php if ($pageNum==1) { echo('w3-text-area-blue'); } ?>">
        <a href="index.php">
            <i class="fa fa-home"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">Home</div>
        </a>
    </div>

    <div class="bottomNavBtnParent <?php if ($pageNum==2) { echo('w3-text-area-blue'); } ?>">
        <a href="schedule.php">
            <i class="fa fa-calendar-o"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">Schedule</div>
        </a>
    </div>

    <div class="bottomNavBtnParent <?php if ($pageNum==3) { echo('w3-text-area-blue'); } ?>">
        <a href="contact_book.php">
            <i class="fa fa-address-book"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">Referrals</div>
        </a>
        <div style="height: 0; width: 100%;">
            <div id="followup_reddot" class="w3-circle w3-red w3-notification-dot"<?php if (count(getFollowUps()) < 1) { echo(' style="display: none;"'); } ?>></div>
        </div>
    </div>

    <div class="bottomNavBtnParent <?php if ($pageNum==4) { echo('w3-text-area-blue'); } ?>">
        <a href="unclaimed_referrals.php">
            <i class="fa fa-bell"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">Unclaimed</div>
        </a>
        <div style="height: 0; width: 100%;">
            <div id="reddot" class="w3-circle w3-red w3-notification-dot"<?php if (count(getUnclaimed()) < 1) { echo(' style="display: none;"'); } ?>></div>
        </div>
    </div>

    <div class="bottomNavBtnParent <?php if ($pageNum==5) { echo('w3-text-area-blue'); } ?>">
        <a href="shop.php">
            <i class="fa-solid fa-tag"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">Shop</div>
        </a>
    </div>

</div>
<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-messaging.js";

    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyDRZODMeGkv_ce8U2iU4gh8YPcKIdcJuCw",
        authDomain: "referral-suite-68718.firebaseapp.com",
        projectId: "referral-suite-68718",
        storageBucket: "referral-suite-68718.appspot.com",
        messagingSenderId: "609271106980",
        appId: "1:609271106980:web:9c1c5cf06818d581b28531"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);

    const messaging = getMessaging();
    navigator.serviceWorker.register('sw.js');
    const SWregistration = await navigator.serviceWorker.ready;

    getToken(messaging, {
        serviceWorkerRegistration: SWregistration,
        vapidKey: "BN2yWUCkOtoNVl5V9dwHj6rLYQn7-1YsnZw1Fpmc84hUtxKxd40JDC3XKw-uiByi95XXnZHhvTLAwd-lbtcpYvQ"
    })
    .then((currentToken) => {
        if (currentToken) {
            sendNewTokenToServer(currentToken);
        } else {
            // Show permission request UI
            alert('No registration token available. Request permission to generate one.');
        }
    })
    .catch((err) => {
        alert('An error occurred while retrieving token.');
        alert(err);
        // ...
    });
    async function sendNewTokenToServer(tok) {
        const res = await fetch('php_functions/addSubToken.php?teamId=<?php echo($__TEAM->id); ?>' + '&token=' + encodeURI(tok));
        const msg = await res.text();
        if (res.status == 200) {
            alert('subscription token saved: ', tok);
        } else if (res.status == 202) {
            console.log(msg);
            console.log(tok);
        } else {
            console.error('subscription token saving error: ', msg);
        }
    }
</script>
<?php } ?>