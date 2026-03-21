import type { Config, RequestOptions } from "./types/index.js";
/** Class used to perform HTTP requests. */
export declare class HttpRequests {
    #private;
    constructor(config: Config);
    /** Request with GET. */
    get<T = unknown>(options: RequestOptions): Promise<T>;
    /** Request with POST. */
    post<T = unknown>(options: RequestOptions): Promise<T>;
    /** Request with PUT. */
    put<T = unknown>(options: RequestOptions): Promise<T>;
    /** Request with PATCH. */
    patch<T = unknown>(options: RequestOptions): Promise<T>;
    /** Request with DELETE. */
    delete<T = unknown>(options: RequestOptions): Promise<T>;
    /** Request with POST that returns a stream. */
    postStream(options: RequestOptions): Promise<ReadableStream<Uint8Array>>;
}
//# sourceMappingURL=http-requests.d.ts.map