import { MeiliSearchError } from "./meilisearch-error.js";
/** Error thrown when a HTTP request times out. */
export declare class MeiliSearchRequestTimeOutError extends MeiliSearchError {
    name: string;
    cause: {
        timeout: number;
        requestInit: RequestInit;
    };
    constructor(timeout: number, requestInit: RequestInit);
}
//# sourceMappingURL=meilisearch-request-timeout-error.d.ts.map