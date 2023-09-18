<?php

namespace App\Support\LaravelBalance\Dto;

class TransactionDto
{
  private int $amount;
  private string $type;
  private ?string $transaction_title;
  private ?string $description;
  private array $data;
  private array $relatedMorph;
  /**
   * TransactionDto constructor.
   *
   * @param int $amount
   * @param string $type
   * @param string|null $transaction_title
   * @param string|null $description
   * @param array $data
   */

  public function __construct(int $amount, string $type, string $transaction_title = null, string $description = null, array $data = [], array $relatedMorph = [])
  {
    $this->amount = $amount;
    $this->type = $type;
    $this->transaction_title = $transaction_title;
    $this->description = $description;
    $this->data = $data;
    $this->relatedMorph = $relatedMorph;
  }

  /**
   * @return int
   */
  public function getAmount(): int
  {
    return $this->amount;
  }

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @return string|null
   */

  public function getTransactionTitle(): ?string
  {
    return $this->transaction_title;
  }

  /**
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * @return array
   */

  public function getRelatedMorph(): array
  {
    return $this->relatedMorph;
  }
}
