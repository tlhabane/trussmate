import { ReactSelectSingleOption } from '../types';

export const bankNames = [
    {
        bankName: 'ABSA',
        branchCode: '632005',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'African Bank',
        branchCode: '430000',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Bidvest',
        branchCode: '462005',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Capitec',
        branchCode: '470010',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'First National Bank',
        branchCode: '250655',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Investec',
        branchCode: '580105',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Meeg Bank',
        branchCode: '471001',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Nedbank',
        branchCode: '198765',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'PostBank',
        branchCode: '460005',
        countryCode: 'ZA',
        country: 'South Africa',
    },
    {
        bankName: 'Standard Bank',
        branchCode: '051001',
        countryCode: 'ZA',
        country: 'South Africa',
    },
];

export const bankOptions: ReactSelectSingleOption[] = bankNames.map(({ bankName }) => ({
    value: bankName,
    label: bankName,
}));
