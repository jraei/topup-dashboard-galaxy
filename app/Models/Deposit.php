
<?php

namespace App\Models;

use App\Models\User;
use App\Models\PayMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pay_method(): BelongsTo
    {
        return $this->belongsTo(PayMethod::class);
    }

    /**
     * Scope for backend-powered filtering, sorting, searching for deposit datatable
     */
    public function scopeWithFilters($query, $request)
    {
        // Filter by current authenticated user only
        $query->where('user_id', auth()->id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Date range filter (expects date_range[] = [start, end])
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('created_at', [
                $request->get('date_start'),
                $request->get('date_end'),
            ]);
        }

        // Search (in id, reference, or pay_method)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('pay_method', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
