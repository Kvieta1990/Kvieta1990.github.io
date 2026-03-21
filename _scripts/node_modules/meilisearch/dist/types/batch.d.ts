import type { Batch, BatchesResults, TasksOrBatchesQuery } from "./types/index.js";
import type { HttpRequests } from "./http-requests.js";
/**
 * Class for handling batches.
 *
 * @see {@link https://www.meilisearch.com/docs/reference/api/batches}
 */
export declare class BatchClient {
    #private;
    constructor(httpRequests: HttpRequests);
    /** {@link https://www.meilisearch.com/docs/reference/api/batches#get-one-batch} */
    getBatch(uid: number): Promise<Batch>;
    /** {@link https://www.meilisearch.com/docs/reference/api/batches#get-batches} */
    getBatches(params?: TasksOrBatchesQuery): Promise<BatchesResults>;
}
//# sourceMappingURL=batch.d.ts.map