import type { WaitOptions, TasksOrBatchesQuery, TasksResults, Task, DeleteOrCancelTasksQuery, EnqueuedTaskPromise, TaskUidOrEnqueuedTask, ExtraRequestInit } from "./types/index.js";
import type { HttpRequests } from "./http-requests.js";
/**
 * Class for handling tasks.
 *
 * @see {@link https://www.meilisearch.com/docs/reference/api/tasks}
 */
export declare class TaskClient {
    #private;
    constructor(httpRequest: HttpRequests, defaultWaitOptions?: WaitOptions);
    /** {@link https://www.meilisearch.com/docs/reference/api/tasks#get-one-task} */
    getTask(uid: number, extraRequestInit?: ExtraRequestInit): Promise<Task>;
    /** {@link https://www.meilisearch.com/docs/reference/api/tasks#get-tasks} */
    getTasks(params?: TasksOrBatchesQuery): Promise<TasksResults>;
    /**
     * Wait for an enqueued task to be processed. This is done through polling
     * with {@link TaskClient.getTask}.
     *
     * @remarks
     * If an {@link EnqueuedTask} needs to be awaited instantly, it is recommended
     * to instead use {@link EnqueuedTaskPromise.waitTask}, which is available on
     * any method that returns an {@link EnqueuedTaskPromise}.
     */
    waitForTask(taskUidOrEnqueuedTask: TaskUidOrEnqueuedTask, options?: WaitOptions): Promise<Task>;
    /**
     * Lazily wait for multiple enqueued tasks to be processed.
     *
     * @remarks
     * In this case {@link WaitOptions.timeout} is the maximum time to wait for any
     * one task, not for all of the tasks to complete.
     */
    waitForTasksIter(taskUidsOrEnqueuedTasks: Iterable<TaskUidOrEnqueuedTask> | AsyncIterable<TaskUidOrEnqueuedTask>, options?: WaitOptions): AsyncGenerator<Task, void, undefined>;
    /** Wait for multiple enqueued tasks to be processed. */
    waitForTasks(...params: Parameters<typeof this.waitForTasksIter>): Promise<Task[]>;
    /** {@link https://www.meilisearch.com/docs/reference/api/tasks#cancel-tasks} */
    cancelTasks(params: DeleteOrCancelTasksQuery): EnqueuedTaskPromise;
    /** {@link https://www.meilisearch.com/docs/reference/api/tasks#delete-tasks} */
    deleteTasks(params: DeleteOrCancelTasksQuery): EnqueuedTaskPromise;
}
type PickedHttpRequestMethods = Pick<HttpRequests, "post" | "put" | "patch" | "delete">;
export type HttpRequestsWithEnqueuedTaskPromise = {
    [TKey in keyof PickedHttpRequestMethods]: (...params: Parameters<PickedHttpRequestMethods[TKey]>) => EnqueuedTaskPromise;
};
export declare function getHttpRequestsWithEnqueuedTaskPromise(httpRequest: HttpRequests, taskClient: TaskClient): HttpRequestsWithEnqueuedTaskPromise;
export {};
//# sourceMappingURL=task.d.ts.map