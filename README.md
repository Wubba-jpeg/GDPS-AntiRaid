# GDPS antiraid
### Anti-raid for pre 1.9 & 2.0+ GDPSs
### currently only supports [MegaSa1nt's GMDprivateServer fork](https://github.com/MegaSa1nt/GMDprivateServer).
> [!IMPORTANT]  
> I highly recommend you activate hcaptcha and turn off the preactivation of accounts (E-mail verification)

## What's Added In This Fork?
- Added customization for webhook messages
- Added customization for webhook ping message

## This Modification:
- Adds rate limits (configurable)
- Blocks invalid UDIDs
- Blocks users with impossibly high star counts
- Blocks level names that are valid base64
- Blocks levels with "realcq" or "furrycq" in the name
- Logs raid attempts
- Removes like bots
- Removes comment bots
- Removes account spam

if you want to make changes, feel free to submit a pull request. for feature requests or bug reports, open an issue.

## Setup
1. download the latest [release](https://github.com/Wubba-jpeg/gdps-antiraid/releases) and paste everything in your GDPS folder
2. configure everything in config/raidfix.php
3. Go to your phpmyadmin and enter the following sql:
 ```sql
CREATE TABLE likes (
    likeID INT AUTO_INCREMENT PRIMARY KEY,
    timestamp INT NOT NULL
);
```

## Planned features
- none at this time.

**if you find this useful consider starring for this to be more well known!!**
