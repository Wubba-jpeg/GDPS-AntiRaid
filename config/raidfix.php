<?php
// RAID FIX/RATE LIMIT CONFIG

//here, put the amount of levels you want to allow each hour before requests are denied
$levelperhr = 10;
// comments per hr
$commentperhr = 15;
// likes each hr
$likesperhr = 10;
// accounts per hr
$accperhr = 10;
// when submitting your stars, if your stars are above the max amount of stars one can possibly gain, you're blocked. however, a buffer is here for dailies / weeklies / events, etc. (if you're even using this for 1.9+).
$starsbuffer = 100;
// if you use accounts, set this to 1
$usesaccounts = 0;


// DISCORD WEBHOOK CONFIG

// webhook where logs for raids are sent. if you don't want these, keep this empty
$discordWebhook = "";

// FOR ALL THE CUSTOM MESSAGES

// custom message for the account rate limit, keep this empty for default preset
// var: $count, $max (eg: $count/$max accounts in the past hour | 3/7 accounts in the past hour)
$webhookMessageAccount = "";
$webhookMessageComment = "";


// if you want it to ping <@&role> or <@user> put it here (must put it in discord format, <@&roleid> or <@userid>)
$webhookMessageAccountPing = "";
$webhookMessageCommentPing = "";


// I'll (soon) add more choices here. if you want anything to be added, make an issue in the repo
