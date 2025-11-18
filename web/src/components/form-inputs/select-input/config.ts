import { StylesConfig } from 'react-select';

export function Styles<Option>() {
    const customSelectStyles: StylesConfig<Option> = {
        control: (base, state) => ({
            ...base,
            fontFamily:
                '-apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
            fontSize: '14px',
            fontWeight: 'normal',
            lineHeight: '20px',
            height: '50px',
            width: '100%',
            margin: 0,
            padding: '15px 0',
            borderRadius: 2,
            // Overwrites the different states of border
            borderColor: state.isFocused ? 'transparent' : 'transparent',
            /*border: state.isFocused || state.menuIsOpen ? '1px solid #0c6baf' : '1px solid rgba(6, 18, 35, 0.14)',*/
            /*borderTopColor: state.isFocused || state.menuIsOpen ? '#0c6baf' : 'rgba(0, 0, 0, 0.14)',*/
            boxShadow: state.isFocused ? 'none' : 'none',
            '&:hover': {
                // Overwrites the different states of border
                border: '1px solid rgba(6, 18, 35, 0.14)',
                borderTopColor: 'rgba(0, 0, 0, 0.14)',
            },
            '&:placeholder': {
                opacity: '0.77',
                fontWeight: '400',
            },
        }),
        menu: (provided) => ({
            ...provided,
            borderRadius: 2,
            width: '100%',
        }),
        menuPortal: (provided) => ({ ...provided, zIndex: 9999 }),
        option: (provided, state) => ({
            ...provided,
            padding: 10,
            fontSize: '14px',
            fontWeight: '400',
            lineHeight: '20px',
            // eslint-disable-next-line no-nested-ternary
            color: state.isSelected ? '#ffffff' : state.isDisabled ? '#a3a3a5' : '#525252',
            backgroundColor: state.isSelected ? '#353f4d' : 'transparent',
            '&:hover': {
                // Overwrites the different states of border
                // eslint-disable-next-line no-nested-ternary
                backgroundColor: state.isSelected ? '#353f4d' : state.isDisabled ? 'transparent' : '#e3e3e5',
                // eslint-disable-next-line no-nested-ternary
                color: state.isSelected ? '#ffffff' : state.isDisabled ? '#a3a3a5' : '#525252',
            },
        }),
        groupHeading: (provided) => ({
            ...provided,
            fontFamily:
                '-apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
            fontSize: '14px',
            fontWeight: '400',
            lineHeight: '20px',
            color: '#525252',
            padding: '15px 10px',
            margin: 0,
            backgroundColor: 'rgba(0, 0, 0, 0.065)',
            textTransform: 'capitalize',
            borderTop: '1px solid rgba(0, 0, 0, 0.075)',
            borderBottom: '1px solid rgba(0, 0, 0, 0.075)',
        }),
    };

    return customSelectStyles;
}
