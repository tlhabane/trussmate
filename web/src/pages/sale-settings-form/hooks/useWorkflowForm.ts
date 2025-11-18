import { useForm, useFormElements } from '../../../utils';
import { workflowFormConfig } from '../config';
import { Workflow } from '../../../models';

export const useWorkflowForm = () => {
    const form = useForm(workflowFormConfig);
    const getElement = useFormElements<Workflow>();
    
    return { ...form, getElement };
}
