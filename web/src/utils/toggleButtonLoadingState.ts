let actionButtonTooltipClass: string | null = null;

export const toggleButtonLoadingState = (actionButton?: HTMLElement) => {
    if (actionButton && actionButton.tagName.toLowerCase() === 'button') {
        if (actionButtonTooltipClass) {
            actionButton.classList.toggle(actionButtonTooltipClass);
            actionButtonTooltipClass = null;
        } else {
            ['tooltip-top', 'tooltip-bottom', 'tooltip-right', 'tooltip-left'].forEach((toolTip) => {
                if (actionButton.classList.contains(toolTip)) {
                    actionButtonTooltipClass = toolTip;
                    actionButton.classList.toggle(toolTip);
                }
            });
        }
        actionButton.classList.toggle('loading');
    }
};
