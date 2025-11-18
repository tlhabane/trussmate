import { useForm, useFormElements } from '../../utils';
import { TaskMonitor } from '../../models';
import { taskMonitorFormConfig } from './task-monitor-form-config';

export const useTaskMonitorForm = () => {
    const form = useForm(taskMonitorFormConfig);
    const getElement = useFormElements<TaskMonitor>();
    
    return { ...form, getElement };
};
