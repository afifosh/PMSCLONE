<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Fields;

use Akaunting\Money\Currency;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Table\NumericColumn;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Innoclapps\Contracts\Fields\Customfieldable;

class Numeric extends Field implements Customfieldable
{
    /**
     * This field support input group
     *
     * @var boolean
     */
    public bool $supportsInputGroup = true;

    /**
     * Field component
     */
    public ?string $component = 'numeric-field';

    /**
     * Field currency
     *
     * @var null|\Akaunting\Money\Currency
     */
    public null|Currency $currency = null;

    /**
     * Initialize Numeric field
     *
     * @param string $attribute
     * @param string|null $label
     */
    public function __construct($attribute, $label = null)
    {
        parent::__construct($attribute, $label);

        $this->rules('nullable', 'numeric', 'decimal:0,3', 'min:0')
            ->prepareForValidation(function ($value, $request, $validator) {
                return with($value, function ($value) {
                    if (Innoclapps::importStatus() === false) {
                        return $value;
                    }

                    return $this->ensureProperImportValue($value);
                });
            })->withMeta(['attributes' => ['placeholder' => '--']])
              ->provideSampleValueUsing(fn () => rand(20000, 40000));
    }

    /**
     * Resolve the actual field value
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function resolve($model)
    {
        if (is_callable($this->resolveCallback)) {
            return call_user_func_array($this->resolveCallback, [$model, $this->attribute]);
        }

        $value = $model->{$this->attribute};

        return is_null($value) ? $value : (float) $value;
    }

    /**
     * Resolve the displayable field value
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function resolveForDisplay($model)
    {
        $value = $this->resolve($model);

        if (is_callable($this->displayCallback)) {
            return call_user_func_array($this->displayCallback, [$model, $value]);
        }

        if (! is_float($value)) {
            $value = (float) $value;
        }

        if ($this->currency) {
            $value = to_money($value, $this->currency)->format();
        }

        return $value;
    }

    /**
     * Set the field currency
     *
     * @param string|null|\Akaunting\Money\Currency|null $currency
     *
     * @return static
     */
    public function currency(string|null|Currency $currency = null) : static
    {
        if (is_string($currency) || is_null($currency)) {
            $currency = new Currency($currency ?: Innoclapps::currency());
        }

        $this->currency = $currency;

        $method = $currency->isSymbolFirst() ? 'inputGroupPrepend' : 'inputGroupAppend';

        return $this->{$method}($currency->getCurrency());
    }

    /**
     * Get the field value for the given request
     *
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $request
     * @param string $requestAttribute
     *
     * @return mixed
     */
    public function attributeFromRequest(ResourceRequest $request, $requestAttribute) : mixed
    {
        $value = parent::attributeFromRequest($request, $requestAttribute);

        return floatval($value) > 0 ? $value : null;
    }

    /**
     * Provide the column used for index
     */
    public function indexColumn() : NumericColumn
    {
        return (new NumericColumn($this->attribute, $this->label))
            ->currency($this->currency);
    }

    /**
     * Create the custom field value column in database
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $fieldId
     *
     * @return void
     */
    public static function createValueColumn($table, $fieldId)
    {
        $table->decimal($fieldId, 15, 3)->index()->nullable();
    }

    /**
     * Set the numeric field decimal precision
     *
     * @param int $precision
     *
     * @return static
     */
    public function precision($precision)
    {
        return $this->withMeta(['attributes' => ['precision' => $precision]]);
    }

    /**
     * The function formats a numeric field for .csv export
     * The format will be performed with the currency iso code
     * for better readibility and e.q. usage on other systems
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return string
     */
    public function resolveForExport($model)
    {
        $value = $this->resolve($model);

        if (is_null($value)) {
            $value = 0;
        }

        if (! $this->currency) {
            return $value;
        }

        $currency = $this->currency;

        $negative     = $value < 0;
        $currencyName = $currency->getCurrency();

        $amount    = $negative ? -$value : $value;
        $thousands = $currency->getThousandsSeparator();
        $decimals  = $currency->getDecimalMark();

        $prefix = ! $currency->isSymbolFirst() ? '' : $currencyName;
        $suffix = $currency->isSymbolFirst() ? '' : ' ' . $currencyName;

        $value = number_format($amount, $currency->getPrecision(), $decimals, $thousands);

        return ($negative ? '-' : '') . $prefix . $value . $suffix;
    }

    /**
     * Unformat the given number
     *
     * @param mixed $number
     * @param boolean $forceNumber
     * @param string $decimalPoint
     * @param string $thousandSeparator
     *
     * @return mixed
     */
    protected function unformatNumber($number, $forceNumber = true, $decimalPoint = '.', $thousandSeparator = ',')
    {
        if ($forceNumber) {
            $number = preg_replace('/^[^\d]+/', '', $number);
        } elseif (preg_match('/^[^\d]+/', $number)) {
            return null;
        }

        $type   = (strpos($number, $decimalPoint) === false) ? 'int' : 'float';
        $number = str_replace([$decimalPoint, $thousandSeparator], ['.', ''], $number);

        settype($number, $type);

        return $number;
    }

    /**
     * Ensure that the given value is formatted for import
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function ensureProperImportValue($value)
    {
        if (is_null($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        // First, we will check if the value starts with currency
        $startingCurrency = substr(trim($value), 0, 3);
        $currencyCode     = null;
        if (is_string($startingCurrency) && ctype_alpha($startingCurrency)) {
            $currencyCode = $startingCurrency;
        } else {
            // If the value does not starts with currency
            // let's check if it ends with currency
            $endingCurrency = substr(trim($value), -3);
            if (is_string($endingCurrency) && ctype_alpha($endingCurrency)) {
                $currencyCode = $endingCurrency;
            }
        }

        // If currency is found, we will strip the currency code from the
        // value and return unly the unformatted number for storage
        if ($currencyCode && $currency = config('money.' . strtoupper($currencyCode))) {
            return $this->unformatNumber($value, true, $currency['decimal_mark'], $currency['thousands_separator']);
        }

        return $value;
    }
}
