const ApiService = Shopware.Classes.ApiService;

class ApiKeyVerifyService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'vis/search') {
        super(httpClient, loginService, apiEndpoint);
    }

    verifyKey() {
        const apiRoute = `/_action/${this.getApiBasePath()}/api_key_verify`;
        return this.httpClient.post(
            apiRoute, {}, {
                baseURL: Shopware.Context.api.apiPath,
                headers: this.getBasicHeaders(),
            },
        ).then((response) => {
            return response;
        });
    }

}

export default ApiKeyVerifyService;
