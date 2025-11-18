import { LinkClickFn } from '../../../types';

/**
 * Handles click events on tabs.
 *
 * @param event - The click or touch event that triggered the function.
 * @returns string - The ID of the target tab pane that was activated.
 */
export const onTabClick: LinkClickFn = (event) => {
    event.preventDefault();
    const targetPanes = event.currentTarget.href.split('/');
    const tabParent = event.currentTarget.offsetParent;
    
    if (targetPanes.length > 0) {
        const targetPane = targetPanes[targetPanes.length - 1];
        if (tabParent) {
            tabParent.childNodes.forEach((tabChild) => {
                tabChild.childNodes.forEach((grandChild) => {
                    const grandChildElement = grandChild as HTMLElement;
                    
                    if (
                        grandChildElement &&
                        grandChildElement.tagName &&
                        grandChildElement.tagName.toLowerCase() === 'a'
                    ) {
                        const tabLink = grandChild as HTMLAnchorElement;
                        if (!tabLink.classList.contains('no-effect')) {
                            if (event.currentTarget.href === tabLink.href) {
                                if (!tabLink.classList.contains('active')) {
                                    tabLink.classList.add('active');
                                }
                            } else {
                                tabLink.classList.remove('active');
                            }
                        }
                    }
                });
            });
            
            const tabContent = tabParent.nextSibling;
            if (tabContent) {
                const htmlTabContent = tabContent as HTMLElement;
                if (htmlTabContent.classList.contains('tab-content')) {
                    tabContent.childNodes.forEach((tabPane) => {
                        const htmlTabPane = tabPane as HTMLElement;
                        if (htmlTabPane && htmlTabPane.classList.contains('tab-pane')) {
                            if (htmlTabPane.id === targetPane) {
                                htmlTabPane.classList.remove('fadeOut');
                                htmlTabPane.classList.add('active', 'show', 'fadeIn');
                            } else {
                                htmlTabPane.classList.remove('active', 'show', 'fadeIn');
                                htmlTabPane.classList.add('fadeOut');
                            }
                        }
                    });
                }
            }
        }
        
        return targetPane;
    }
    
    return '';
}
