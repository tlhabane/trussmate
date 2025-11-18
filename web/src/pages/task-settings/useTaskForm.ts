import { useForm, useFormElements } from '../../utils';
import { Task } from '../../models';
import { taskFormConfig } from './taskFormConfig';

export const useTaskForm = () => {
    const form = useForm(taskFormConfig);
    const getElement = useFormElements<Task>();
    
    return { ...form, getElement };
}
