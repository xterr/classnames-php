<?php

namespace xterr\ClassNames;

/**
 * Class ClassNames
 * @package xterr\ClassNames
 */
class ClassNames
{
    /**
     * @var array
     */
    protected $_aResultSet = [];

    public function __invoke()
    {
        $aArgs             = func_get_args();
        $this->_aResultSet = [];
        $aList             = [];

        $this->_parseArray($aArgs);

        foreach ($this->_aResultSet as $key => $val)
        {
            if ($val)
            {
                $aList[] = $key;
            }
        }

        return implode(' ', $aList);
    }

    protected function _parse($mArg, $bDefaultValue = TRUE)
    {
        if (!$mArg)
        {
            return;
        }

        // 'foo bar'
        if (is_string($mArg))
        {
            $this->_parseString($mArg);
        }
        // function() {}
        elseif ($mArg instanceof \Closure || is_callable($mArg))
        {
            $this->_parseCallable($mArg);
        }
        // ['foo', 'bar', ...] || ['foo' => TRUE, 'bar' => FALSE, ...]
        else if (is_array($mArg))
        {
            $this->_parseArray($mArg);
        }
        // stdClass
        elseif (is_object($mArg))
        {
            $this->_parseObject($mArg);
        }
        // '130'
        elseif (is_int($mArg))
        {
            $this->_aResultSet[$mArg] = $bDefaultValue;
        }
        elseif (is_float($mArg))
        {
            $this->_aResultSet[(string)$mArg] = $bDefaultValue;
        }
    }

    protected function _parseString($mArg, $bDefaultValue = TRUE)
    {
        $aArr = preg_split("/\s+/", $mArg, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($aArr as $sClassName)
        {
            $this->_aResultSet[$sClassName] = $bDefaultValue;
        }
    }

    protected function _parseArray (array $aArray)
    {
        foreach ($aArray as $mKey => $mValue)
        {
            if (is_int($mKey))
            {
                if (is_bool($mValue))
                {
                    $this->_parse($mKey, $mValue);
                }
                else
                {
                    $this->_parse($mValue);
                }
            }
            elseif (is_string($mKey))
            {
                if (!is_bool($mValue))
                {
                    throw new \UnexpectedValueException('Value for key ' . $mKey . ' must be of type boolean');
                }

                $this->_parseString($mKey, $mValue);
            }
        }
    }

    protected function _parseObject($oObject)
    {
        if (method_exists($oObject, '__toString'))
        {
            $this->_parse((string) $oObject);
        }
    }

    protected function _parseCallable(callable $cCallable)
    {
        $this->_parse($cCallable($this->_aResultSet));
    }
}
