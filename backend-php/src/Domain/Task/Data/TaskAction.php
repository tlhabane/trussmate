<?php

namespace App\Domain\Task\Data;

enum TaskAction: string
{
    /**
     * Estimate = Estimator provides quotation based on floor plan
     * Invoice = Send invoice based on pre-determined percentage of estimate (project) amount
     * Penalty = Send penalty invoice based on pre-determined percentage of invoice amount
     * Quotation = Send quotation based on estimate provided by Estimator(s)
     * TASK = Any task (except from estimate) that requires a person or team to execute
     */
    case ESTIMATE = 'estimate';
    case INVOICE = 'invoice';
    case PENALTY = 'penalty';
    case PROFORMA = 'proforma_invoice';
    case QUOTATION = 'quotation';
    case TASK = 'task';
    case NONE = '0';
}
