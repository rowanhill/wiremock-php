<?php

namespace WireMock\Client;

use DateTime;

class ServeEventQuery
{
    /** @var DateTime|null */
    private $_since;
    /** @var int|null */
    private $_limit;
    /** @var boolean|null */
    private $_unmatched;
    /** @var string|null */
    private $_matchingStubId;

    /**
     * @param DateTime $since
     * @return $this
     */
    public function withSince($since)
    {
        $this->_since = $since;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function withLimit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @return $this
     */
    public function withUnmatched()
    {
        $this->_unmatched = true;
        return $this;
    }

    /**
     * @param string $stubId
     * @return $this
     */
    public function withStubMapping($stubId)
    {
        $this->_matchingStubId = $stubId;
        return $this;
    }

    public function toParamsString()
    {
        $params = [];
        if ($this->_since) {
            $params[] = 'since=' . urlencode($this->_since->format(DateTime::ATOM));
        }
        if ($this->_limit) {
            $params[] = 'limit=' . urlencode($this->_limit);
        }
        if ($this->_unmatched) {
            $params[] = 'unmatched=true';
        }
        if ($this->_matchingStubId) {
            $params[] = 'matchingStub=' . urlencode($this->_matchingStubId);
        }
        return join('&', $params);
    }

    /**
     * @param DateTime|null $since
     * @param int|null $limit
     * @return self
     */
    public static function paginated($since, $limit = null)
    {
        $result = new self();
        if ($since) {
            $result->withSince($since);
        }
        if ($limit) {
            $result->withLimit($limit);
        }
        return $result;
    }

    /**
     * @return self
     */
    public static function unmatched()
    {
        $result = new self();
        return $result->withUnmatched();
    }

    /**
     * @param string $stubId
     * @return self
     */
    public static function forStubMapping($stubId)
    {
        $result = new self();
        return $result->withStubMapping($stubId);
    }
}