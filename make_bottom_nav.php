<?php
require_once('require_area.php');
function make_bottom_nav($pageNum, $bottomSpacingPX='80px') { ?>

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
        <a href="sync.html">
            <i class="business-suite"></i>
            <div class="w3-tiny w3-opacity" style="height: 0;">B S</div>
        </a>
    </div>

</div>

<?php } ?>