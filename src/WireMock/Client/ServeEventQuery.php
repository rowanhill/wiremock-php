<?php

namespace WireMock\Client;

use DateTime;

class ServeEventQuery
{
    /** @var DateTime|null */
    private $since;
    /** @var int|null */
    private $limit;
    /** @var boolean|null */
    private $unmatched;
    /** @var string|null */
    private $matchingStubId;

    /**
     * @param DateTime $since
     * @return $this
     */
    public function withSince($since)
    {
        $this->since = $since;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function withLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return $this
     */
    public function withUnmatched()
    {
        $this->unmatched = true;
        return $this;
    }

    /**
     * @param string $stubId
     * @return $this
     */
    public function withStubMapping($stubId)
    {
        $this->matchingStubId = $stubId;
        return $this;
    }

    public function toParamsString()
    {
        $params = [];
        if ($this->since) {
            $params[] = 'since=' . urlencode($this->since->format(DateTime::ATOM));
        }
        if ($this->limit) {
            $params[] = 'limit=' . urlencode($this->limit);
        }
        if ($this->unmatched) {
            $params[] = 'unmatched=true';
        }
        if ($this->matchingStubId) {
            $params[] = 'matchingStub=' . urlencode($this->matchingStubId);
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