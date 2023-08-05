
Statbus (the application) does not record any data about its users, byond what is necessary in order to facilitate a secure and informative experience. Wherever possible, the application relies on the game server database (the external database) to look up information about players and other associated data. This data is only *displayed* by the application and never stored. Under no circumstances is personally identifiable information (PII) such as IP addresses and ComputerIDs retained by the application. Some functionality requires storing user information on a database used exclusively by the application (the internal database), or in ephemeral session files. This functionality will be listed and explained below.

## Externally Accessed Data
In several places, data about the user is accessed from external services that do not include the game database server. This is done as part of the authentication process (OAuth), and is designed to be uninvasive. 

### TGStation Forum Data
When you authenticate to the application via the TGStation forums, some information is transmitted back to the application and securely stored as part of your PHP session. The data received from TGStation consists of: 
* Your PHPBB username used on the forums
* Your Byond key, which is your “full”, un-interpreted username from Byond
* Your Byond ckey, a simplified version of the Byond key with everything except letters, numbers, and the `@` character stripped out
* If you’ve previously authenticated to Github or Reddit with your TGStation forum account, your GitHub and Reddit username is also provided to the application. At this moment, nothing is done with your GitHub or Reddit username and this information is discarded.

The only information stored on the application from the TGStation forums is the Byond ckey. 

### Discord Data

Authenticating to the application with Discord returns [information](https://discord.com/developers/docs/resources/user#user-object) about your Discord account. Note that the application specifically does not request access to the email address associated with your Discord user. 

All information from your Discord profile is discarded, except for the `id` field, which is used by the application to look up your information in the external database.

### Session Data

The following data is stored in PHP's session mechanism, and is used to re-authenticate users across multiple visits: 

| Key | Use |
| --- | --- |
| `ckey` | Used to re-authenticate users across visits to the application |
| `authSource` | Used to determine how the user authenticated themselves |

This information can be deleted by [logging out](/logout). Session data is also purged automatically after 30 days of inactivity.

### Logs
Webserver logs are not retained in any meaningful way. 

The application has a facility to support error logging. This is disabled by default, and only used when necessary to debug an issue. In those cases, logging is temporarily enabled, and once it is no longer needed, it is disable, and any generated logs are removed.

The following pieces of PII are collected in error logs:

| Key | Use |
| --- | --- |
| `url` | The URL the user was visiting that triggered the error |
| `ip` | The IP address of the user who triggered the error |
| `ckey` | The ckey, if the user is logged in |
### Source Code
The application source code is publicly available on [GitHub](https://github.com/statbus/statbus). Please feel free to contact me with any inconsistencies or questions. The greatest effort has been made to make this document as accurate as possible, but there may be omissions or incorrect information.

**1.1 Rev. 2023-08-05**