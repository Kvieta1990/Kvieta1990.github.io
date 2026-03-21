export type CursorResults<T> = {
    results: T[];
    limit: number;
    from: number;
    next: number;
    total: number;
};
/** Deeply map every property of a record to itself making it partial. */
export type DeepPartial<T> = T extends object ? {
    [TKey in keyof T]?: DeepPartial<T[TKey]>;
} : T;
export type PascalToCamelCase<S extends string> = Uncapitalize<S>;
export type SafeOmit<T, K extends keyof T> = Omit<T, K>;
export type OptionStarOr<T> = "*" | T | null;
export type OptionStarOrList<T extends any[]> = ["*"] | T | null;
//# sourceMappingURL=shared.d.ts.map