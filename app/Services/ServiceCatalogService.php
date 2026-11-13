<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceCatalogService
{
    /**
     * Ambil data detail layanan beserta persyaratan wajib/opsional, ulasan publik, dan layanan terkait.
     *
     * @return array<string, mixed>
     */
    public function buildServiceDetailData(Service $service, ServiceRequirementSyncService $requirementSync): array
    {
        $service->loadMissing('category');

        $requirements = $requirementSync->ensureForService($service);
        $mandatoryRequirements = $requirements->where('is_mandatory', true)->values();
        $optionalRequirements = $requirements->where('is_mandatory', false)->values();

        $publishedReviews = Review::with('user')
            ->where('status', 'published')
            ->where(function ($q) use ($service): void {
                $q->where('service_id', $service->id)
                    ->orWhereHas('order', fn ($oq) => $oq->where('service_id', $service->id));
            })
            ->latest()
            ->get();

        $avgRating = $publishedReviews->count() > 0
            ? round((float) $publishedReviews->avg('rating'), 1)
            : 0.0;

        $relatedServices = Service::with('category')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->take(3)
            ->get();

        return compact(
            'service',
            'mandatoryRequirements',
            'optionalRequirements',
            'publishedReviews',
            'avgRating',
            'relatedServices'
        );
    }

    /**
     * Ambil data katalog layanan, kategori, filter pencarian/rating, dan voucher promo aktif dari database.
     *
     * @return array<string, mixed>
     */
    public function buildCatalogData(Request $request): array
    {
        $categories = Category::withCount('services')
            ->has('services')
            ->orderBy('name')
            ->get();

        $search = trim((string) $request->query('search', ''));
        $selectedCategory = trim((string) $request->query('category', 'all'));
        $selectedRating = trim((string) $request->query('rating', 'all'));

        $servicesQuery = Service::with(['category', 'requirements'])
            ->withCount(['publishedReviews'])
            ->withAvg('publishedReviews', 'rating');

        if ($search !== '') {
            $servicesQuery->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('requirements_info', 'like', '%'.$search.'%');
            });
        }

        if ($selectedCategory !== '' && $selectedCategory !== 'all') {
            $servicesQuery->whereHas('category', function ($q) use ($selectedCategory): void {
                $q->where('slug', $selectedCategory);
            });
        }

        $services = $servicesQuery->latest()->get();

        if ($selectedRating !== '' && $selectedRating !== 'all' && is_numeric($selectedRating)) {
            $targetRating = (int) $selectedRating;
            $services = $services->filter(function (Service $srv) use ($targetRating): bool {
                $avg = (float) ($srv->published_reviews_avg_rating ?? 0);

                return (int) round($avg) === $targetRating;
            })->values();
        }

        $promos = Promo::available()
            ->orderByDesc('is_featured_homepage')
            ->orderByDesc('created_at')
            ->get();

        $featuredPromo = $promos->first();

        return compact(
            'categories',
            'services',
            'promos',
            'featuredPromo',
            'search',
            'selectedCategory',
            'selectedRating'
        );
    }
}
