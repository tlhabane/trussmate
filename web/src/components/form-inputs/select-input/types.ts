export interface SelectSingleOptionType {
    readonly label: string;
    readonly value: string;
    readonly enabled?: boolean;
}

export interface SelectGroupOptionType {
    readonly label: string;
    readonly options: SelectSingleOptionType[];
}

export type SelectOptionType = SelectSingleOptionType | SelectGroupOptionType;
