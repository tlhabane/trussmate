import { useForm, useFormElements } from '../../../utils';
import { WorkflowTask } from '../../../models';
import { FormState } from '../../../types';

export const useWorkflowTaskForm = (task: FormState<WorkflowTask>) => {
    const form = useForm(task);
    const getElement = useFormElements<WorkflowTask>();
    
    return { ...form, getElement };
}
