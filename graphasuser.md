# Graph as user

In this document I will try to exaplain what you must to do to be able to use the [Microsft Graph API](https://docs.microsoft.com/en-us/graph/overview?view=graph-rest-1.0) as user (O365/Azure Login).

How it works?

- Register you app in Azure AD to obtain OAUTH_APP_ID, OAUTH_APP_PASSWORD and setup Redirect URIs.
- Using these values you can try to login at AAD signin page.
- If successful AAD redirect you to callback and in request in authcode. With auth code you can ask for token.
- With the Access token you can use Microsoft Graph to obtain all information where you granted permission for daemon (background service) use.

## Azure configuration

- Register your app in Azure AD and setup all required permissions or check them after first use. [Instructions](https://docs.microsoft.com/en-us/graph/auth-v2-user?view=graph-rest-1.0), [Instructions nr.2](https://docs.microsoft.com/en-us/graph/tutorials/php?tutorial-step=2)
- Define Redirect URI (the callback address) - can be like that for testing http://localhost:8000/callback

## Nette configuration

local.neon *(do not share this)*

- OAUTH_APP_ID - This is unique ID of your application
- OAUTH_APP_PASSWORD - Password for your application
- OAUTH_AUTHORITY - String which contains unique name of your tenant
- OAUTH_REDIRECT_URI - Callback address where you are redirected in case of successful Azure login. (callback method)
- OAUTH_SCOPES - The scope of required permissions. Must be set in "API permissions" under Azure Registered app settings. Required permissions are defined in Microsoft Graph API

common.neon

- OAUTH_TOKEN_ENDPOINT - The address postfix for token access
- OAUTH_AUTHORIZE_ENDPOINT - The address postfix for authorization

Another links
