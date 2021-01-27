# Graph as daemon (Background service)

In this document I will try to exaplain what you must to do to be able to use the [Microsft Graph API](https://docs.microsoft.com/en-us/graph/overview?view=graph-rest-1.0) as daemon (Background service).

How it works?

- Register you app in Azure AD to obtain OAUTH_APP_ID, OAUTH_APP_PASSWORD.
- Using these values you can ask Microsoft oAuth for Access token.
- With the Access token you can use Microsoft Graph for all information where you granted permission for daemon (background service) use.

## Azure configuration

- Register your app in Azure AD and setup all required permissions or check them after first use. [Instructions](https://docs.microsoft.com/en-us/graph/auth-v2-service?view=graph-rest-1.0)

## Nette configuration

local.neon *(do not share this)*

- OAUTH_APP_ID - This is unique ID of your application
- OAUTH_APP_PASSWORD - Password for your application
- OAUTH_AUTHORITY - String which contains unique name of your tenant

common.neon

- OAUTH_TOKEN_ENDPOINT - The address postfix for token access
