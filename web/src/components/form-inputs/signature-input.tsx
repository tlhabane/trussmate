import React, { useCallback, useEffect, useImperativeHandle, useState } from 'react';
import {Button} from '../button';
import { InputContainer } from './input-container';
import { ButtonClickFn } from '../../types';
const { SignatureCanvas } = require('react-signature-canvas');

interface Props {
    error?: string;
    toggleRequiredClass?: boolean;
    onSignature?: (name: string, signature: string) => void;
    id?: string;
    name?: string;
    label?: string;
    required?: boolean;
    value?: string;
}
export const SignatureInput = React.forwardRef<typeof SignatureCanvas, Props>((props, ref) => {
    const { error, id, name, label, onSignature, required, toggleRequiredClass, value,  ...rest} = props;
    // Define the type for the ref to ensure it matches SignatureCanvas
    const signaturePad = React.useRef<typeof SignatureCanvas | null>(null);
    const [isPadEmpty, setIsPadEmpty] = useState(true);

    const checkForSignature = useCallback(() => {
        if (signaturePad && signaturePad.current) {
            setIsPadEmpty(signaturePad.current?.isEmpty());
            const base64Signature = signaturePad.current?.getTrimmedCanvas()?.toDataURL('image/png');
            if (onSignature && base64Signature) {
                onSignature(name || '', base64Signature);
            }
        }
    }, [onSignature, name]);
    
    const handleClear: ButtonClickFn<void> = useCallback((event) => {
        event.preventDefault();
        if (signaturePad && signaturePad.current) {
            signaturePad.current.clear();
            setIsPadEmpty(true);
            if (onSignature) {
                onSignature(name || '', '');
            }
        }
    }, [onSignature, name]);
    
    useImperativeHandle(ref, () => {
        return signaturePad.current as typeof SignatureCanvas;
    });
    
    useEffect(() => {
        if (signaturePad && signaturePad.current && value?.trim() !== '') {
            if (signaturePad.current?.isEmpty()) {
                signaturePad.current?.fromDataURL(value);
            }
        }
    }, [value])
    
    const hasError = Boolean(error);
    return (
        <>
            <InputContainer
                className={`${(hasError && 'has-error') || ''} ${(toggleRequiredClass && ' toggle-required') || ''}`}
                required={required}
            >
                {label && <label htmlFor={id || name}>{label}</label>}
                <div className="signature-container">
                    <SignatureCanvas
                        ref={signaturePad}
                        penColor='blue'
                        canvasProps={{ className: 'signature-canvas' }}
                        onEnd={checkForSignature}
                        clearOnResize={false}
                        {...rest}
                    />
                    {!isPadEmpty && (
                        <Button type="button" className="btn-danger" onClick={handleClear} disabled={isPadEmpty}>
                            <i className='custom-icon icon icon-only trash' />
                        </Button>
                    )}
                </div>
            </InputContainer>
            {hasError && <label className="error">{error}</label>}
        </>
    );
});

