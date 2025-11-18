import React, { JSX } from 'react';
import { formatPrice } from '../../utils';
import { Job } from '../../models';

type Props = {
    job: Job
}

export const JobView: React.FC<Props> = ({ job }): JSX.Element => {
    const { designInfo, jobDescription, lineItems, subtotal, vat, total } = job;
    
    return (
        <div className='invoice-container'>
            <table className='invoice-info-container' style={{ margin: '15px 0' }}>
                <tr>
                    <td><strong>Design Info:</strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>{jobDescription}</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>{designInfo.description}</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table className='invoice-info-container' style={{ margin: '15px 0' }}>
                <tr>
                    <td>Top Chord Dead Load:</td>
                    <td><strong>{designInfo?.topChordDeadLoad || ''}</strong></td>
                </tr>
                <tr>
                    <td>Bottom Chord Dead Load:</td>
                    <td><strong>{designInfo?.bottomChordDeadLoad || ''}</strong></td>
                </tr>
                <tr>
                    <td>Wind Terrain Category:</td>
                    <td><strong>{designInfo?.windTerrainCategory || ''}</strong></td>
                </tr>
                <tr>
                    <td>Wind Speed:</td>
                    <td><strong>{designInfo?.windSpeed || ''}</strong></td>
                </tr>
                <tr>
                    <td>Wind Pressure:</td>
                    <td><strong>{designInfo?.windPressure || ''}</strong></td>
                </tr>
                <tr>
                    <td>Default Pitch:</td>
                    <td><strong>{designInfo?.pitch || ''}</strong></td>
                </tr>
                <tr>
                    <td>Default Truss Centres:</td>
                    <td><strong>{designInfo?.trussCentres || ''}</strong></td>
                </tr>
                <tr>
                    <td>Default Purlin Centres:</td>
                    <td><strong>{designInfo?.purlinCentres || ''}</strong></td>
                </tr>
                <tr>
                    <td>Default Overhang Length:</td>
                    <td><strong>{designInfo?.overhangLength || ''}</strong></td>
                </tr>
                <tr>
                    <td>Roof Area:</td>
                    <td><strong>{designInfo?.roofArea || ''}</strong></td>
                </tr>
                <tr>
                    <td>Floor Area:</td>
                    <td><strong>{designInfo?.floorArea || ''}</strong></td>
                </tr>
            </table>
            {lineItems && lineItems.map((item) => (
                <table className='line-items-container' key={item.category}>
                    <thead>
                    <tr className='category'>
                        <th className='heading-quantity' colSpan={3}>{item.category}</th>
                        <th className='heading-subtotal'>{formatPrice(item.amount, 'ZAR')}</th>
                    </tr>
                    {item.items.length > 0 && (
                        <tr>
                            <th className='heading-quantity'>Qty</th>
                            <th className='heading-description'>Description</th>
                            <th className='heading-price'>Unit Price</th>
                            <th className='heading-subtotal'>Subtotal</th>
                        </tr>
                    )}
                    
                    </thead>
                    {item.items.length > 0 && (
                        <tbody>
                        {item.items.map((i) => (
                            <tr key={i.name}>
                                <td>{i.quantity}</td>
                                <td>{i.name}</td>
                                <td className='right'>-</td>
                                <td className='bold'>-</td>
                            </tr>
                        ))}
                        </tbody>
                    )}
                </table>
            ))}
            <table className={`line-items-container has-bottom-border has-top-border`}>
                <tbody>
                <tr>
                    <td className='right'>SubTotal</td>
                    <td>ZAR {formatPrice(subtotal)}</td>
                </tr>
                <tr>
                    <td className='right'>Vat</td>
                    <td>ZAR {formatPrice(vat)}</td>
                </tr>
                <tr>
                    <td className='right'>Total</td>
                    <td className='large total'>ZAR {formatPrice(total)}</td>
                </tr>
                </tbody>
            </table>
        </div>
    );
};
