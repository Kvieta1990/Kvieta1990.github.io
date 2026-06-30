import { MeiliSearchError } from "./meilisearch-error.js";
/** Error thrown when a waiting for a task times out. */
export declare class MeiliSearchTaskTimeOutError extends MeiliSearchError {
    name: string;
    cause: {
        taskUid: number;
        timeout: number;
    };
    constructor(taskUid: number, timeout: number);
}
//# sourceMappingURL=meilisearch-task-timeout-error.d.ts.map