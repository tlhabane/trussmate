export const getValidatedData = (data: Record<string, any>) => {
    return Object.entries(data).reduce((acc: Record<string, any>, [key, inputProps]) => {
        const { value } = inputProps;
        acc[key] = typeof value === 'string' ? value.trim() : value;
        return acc;
    }, {});
};
