module.exports = {
    root: true,
    extends: [],
    env: {
        node: true,
        browser: true,
        jest: true,
    },
    parserOptions: {
        project: './tsconfig.json',
    },
    rules: {
        '@typescript-eslint/explicit-module-boundary-types': 'off',
        '@typescript-eslint/no-use-before-define': 'off',
        '@typescript-eslint/no-var-requires': 'off',
        'import/prefer-default-export': 'off',
        'import/no-extraneous-dependencies': 'off',
        'jsx-a11y/label-has-associated-control': 'off',
        'react/prop-types': 'off',
        'react/require-default-props': 'off',
        'react/jsx-props-no-spreading': 'off',
        '@typescript-eslint/no-explicit-any': 'off',
        '@typescript-eslint/ban-ts-comment': 'off',
    },
};
