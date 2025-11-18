export const scrollToElement = (elementId?: string) => {
    if (elementId) {
        const validElement = document.getElementById(elementId);
        if (validElement) {
            const top = validElement.getBoundingClientRect().top + window.scrollY - 50;
            window.scroll({ top, behavior: 'smooth' });
        }
        return;
    }
    
    const currentScroll = document.documentElement.scrollTop || document.body.scrollTop;
    if (currentScroll > 0) {
        window.scrollTo(0, 0);
    }
};
