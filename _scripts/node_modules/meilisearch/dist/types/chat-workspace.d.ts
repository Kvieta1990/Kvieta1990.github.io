import type { HttpRequests } from "./http-requests.js";
import type { ChatWorkspaceSettings, ChatCompletionRequest } from "./types/types.js";
/**
 * Class for handling chat workspaces.
 *
 * @see {@link https://www.meilisearch.com/docs/reference/api/chats}
 */
export declare class ChatWorkspace {
    #private;
    constructor(httpRequests: HttpRequests, workspace: string);
    /**
     * Get the settings of a chat workspace.
     *
     * @experimental
     * @see {@link https://www.meilisearch.com/docs/reference/api/chats#get-chat-workspace-settings}
     */
    get(): Promise<ChatWorkspaceSettings>;
    /**
     * Update the settings of a chat workspace.
     *
     * @experimental
     * @see {@link https://www.meilisearch.com/docs/reference/api/chats#update-chat-workspace-settings}
     */
    update(settings: Partial<ChatWorkspaceSettings>): Promise<ChatWorkspaceSettings>;
    /**
     * Reset the settings of a chat workspace.
     *
     * @experimental
     * @see {@link https://www.meilisearch.com/docs/reference/api/chats#reset-chat-workspace-settings}
     */
    reset(): Promise<void>;
    /**
     * Create a chat completion using an OpenAI-compatible interface.
     *
     * @experimental
     * @see {@link https://www.meilisearch.com/docs/reference/api/chats#chat-completions}
     */
    streamCompletion(completion: ChatCompletionRequest): Promise<ReadableStream<Uint8Array>>;
}
//# sourceMappingURL=chat-workspace.d.ts.map