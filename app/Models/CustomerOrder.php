<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerOrder extends Model
{
    protected $fillable = [
        'daily_order_number',
        'restaurant_table_id',
        'customer_name',
        'customer_phone',
        'customer_note',
        'total_amount',
        'order_status',
        'payment_status',
        'preparing_at',
        'ready_at',
        'served_at',
        'completed_at',
        'paid_at',
        'cancelled_at',
        'staff_note',
    ];

    protected $casts = [
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'served_at' => 'datetime',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // an order can still be cancelled or edited only while it is not paid, completed or cancelled
    public function canBeChanged(): bool
    {
        return !in_array($this->order_status, ['cancelled', 'completed'])
            && $this->payment_status !== 'paid';
    }

    public function cancel(string $by, ?string $reason = null): void
    {
        $this->update([
            'order_status' => 'cancelled',
            'cancelled_at' => now(),
            'staff_note' => $this->noteWith('Cancelled', $by, $reason),
        ]);
    }

    // returns false when it is the only item left (the order should be cancelled instead)
    public function removeItem(OrderItem $item, string $by, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($item, $by, $reason) {
            // lock this order's items so two people cannot remove the last two at once
            $items = $this->orderItems()->lockForUpdate()->get();

            if ($items->count() <= 1) {
                return false;
            }

            $what = 'Removed ' . $item->quantity . 'x ' . $item->item_name_snapshot;

            $item->delete();

            $newTotal = $items->where('id', '!=', $item->id)->sum(function ($other) {
                return $other->unit_price * $other->quantity;
            });

            $this->update([
                'total_amount' => $newTotal,
                'staff_note' => $this->noteWith($what, $by, $reason),
            ]);

            return true;
        });
    }

    // adds one line to the staff note: what happened, who did it, when, and why
    private function noteWith(string $what, string $by, ?string $reason): string
    {
        $line = $what . ' by ' . $by . ' at ' . now()->format('d M H:i')
            . ($reason ? ': ' . $reason : '');

        return trim($this->staff_note . "\n" . $line);
    }
}
