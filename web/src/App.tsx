import React from 'react';
import { Toaster } from 'react-hot-toast';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { DefaultRouter } from './router';

function App() {
    const queryClient = new QueryClient();
    return (
        <QueryClientProvider client={queryClient}>
            <Toaster containerStyle={{ zIndex: 9999 }} position='top-center' reverseOrder={false} />
            <DefaultRouter />
        </QueryClientProvider>
    );
}

export default App;
