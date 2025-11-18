/**
 Capitalise first letter of string or each word in a sentence
 @param {string} str - sentence or word to be separated
 @param {string} strSeparator - sentence or word separator (default = ' ' [empty string])
 @return string
 */
export function capitalizeFirstLetter(str: string, strSeparator = ' ') {
    const sentenceParts = str.split(strSeparator);
    if (sentenceParts.length > 0) {
        const newSentence = sentenceParts.map(
            (phrase) => phrase.charAt(0).toUpperCase() + phrase.slice(1).toLowerCase(),
        );
        return newSentence.join(strSeparator);
    }
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

export function chunkArray<T>(inputArray: T[], chunkSize: number) {
    const chunks: T[][] = [];
    for (let i = 0; i < inputArray.length; i += chunkSize) {
        const chunk = inputArray.slice(i, i + chunkSize);
        chunks.push(chunk);
    }
    
    return chunks;
}

/**
 Format number to n decimal places
 @param {number} num - number to be formatted
 @param {number} decimalPlaces - number of decimal places (default = 2)
 @return string
 */
export function formatNumber(num: number, decimalPlaces = 2) {
    if (!num || Number.isNaN(num) || num === 0) {
        return '0.00';
    }
    const formattedNumber = typeof num.toFixed === 'function' ? num.toFixed(decimalPlaces) : `${num}`;
    const [leader, decimal] = formattedNumber.split('.');
    if (decimalPlaces > 0 && decimal.length < decimalPlaces) {
        const formattedDecimal = decimal ? decimal.padEnd(decimalPlaces, '0') : '0'.repeat(decimalPlaces);
        return `${leader}.${formattedDecimal}`;
    }
    return formattedNumber;
}

/**
 Format number to currency format
 @param {number} price - number to be formatted
 @param {string} currencySymbol - number of decimal places (default = 2)
 @return string
 */
export function formatPrice(price: number, currencySymbol = 'R') {
    const strPrice = formatNumber(Math.abs(price), 2);
    const a = strPrice.split('');
    
    if (price > 1000000000) a.splice(a.length - 12, 0, ',');
    
    if (price > 1000000) a.splice(a.length - 9, 0, ',');
    
    if (price > 1000) a.splice(a.length - 6, 0, ',');
    
    const formattedPrice = currencySymbol + a.join('');
    return price < 0 ? `(${formattedPrice})` : formattedPrice;
}

export function sortDates(dates: string[], order: 'asc' | 'desc' = 'asc') {
    const instancesOfDates = dates.map((date) => new Date(date));
    
    if (order === 'desc') {
        return instancesOfDates.sort((a, b) => b.getTime() - a.getTime());
    }
    
    return instancesOfDates.sort((a, b) => a.getTime() - b.getTime());
}

const SI_SYMBOL = ['', 'k', 'M', 'B', 'T'];

/**
 Abbreviate number to n decimal places
 @param {number} number - number to be formatted
 @param {number} decimalPlaces - number of decimal places (default = 2)
 @return string
 */
export function abbreviateNumber(number: number, decimalPlaces = 2) {
    const i = number === 0 ? 0 : Math.floor(Math.log(number) / Math.log(1024));
    
    // eslint-disable-next-line no-restricted-properties
    return `${(number / Math.pow(1024, i)).toFixed(decimalPlaces)}${SI_SYMBOL[i]}`;
}

export function getDefaultError(message?: string) {
    return message || 'Oops! An error occurred while processing your request, please try again.';
}
