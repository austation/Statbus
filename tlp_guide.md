
#### Status of this Document
The TLP system used by the application is for advisory only and is non-binding. It has not been formally accepted or adopted.

#### Overview

Some data in Statbus (the application) consists of Personally Identifiable Information (PII). This includes IP addresses, Computer IDs (CID), and information voluntarily disclosed to administrators by users. Other data may consist of Reputation Influencing Information (RII). This can include, but is not limited to the contents of ahelp tickets, bans, and notes.

For this reason, the application has adopted a modified version of the [Traffic Light Protocol](https://en.wikipedia.org/wiki/Traffic_Light_Protocol). The following definitions are in-use:

#### TLP:GREEN
Information that is readily available in public logs. There is generally no restriction on sharing this information. Generally speaking, if you can see it in `/parsed-logs`, the information is TLP:GREEN.

#### TLP:AMBER
Information that could be used to identify a player, or cause reputation harm, outside of the context of the application or the game. This information includes, but is not limited to:
- Ahelp tickets
- Bans*
- Notes and Messages

TLP:AMBER information is generally usable in ban appeals without redaction. In other cases, information that can be used to identify participants should be redacted before sharing.

\* Except public bans

#### TLP:RED
Information that can be used to identify a player, outside of the context of the application or the game. This information includes, but is not limited to:
- IP Addresses
- CIDs

TLP:RED information should not be shared with people who are not also authorized to view TLP:RED information. Generally speaking, this information should only be shared with other administrators when necessary. Exceptions are granted for sharing information with other game server administrators in order to facilitate administrative actions.

**1.0 Rev. 2023-08-05**