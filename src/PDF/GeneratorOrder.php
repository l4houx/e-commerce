<?php

namespace App\PDF;

use App\Entity\Shop\Order;
use Jurosh\PDFMerge\PDFMerger;

final class GeneratorOrder extends Generator
{
    /**
     * @param array<string, mixed> $data
     */
    public function generate(string $filename, string $view, array $data = []): string
    {
        /** @var Order $order */
        $order = $data['order'];

        $groupOfOrderDetails = array_chunk($order->getOrderDetails()->toArray(), 15);

        $pdfMerger = new PDFMerger();

        foreach ($groupOfOrderDetails as $page => $orderDetails) {
            $pdfMerger->addPDF(
                sprintf(
                    '%s/%s',
                    $this->publicDirectory,
                    $this->generatePage(
                        $filename.'-p'.($page + 1),
                        $view,
                        [
                            'order' => $order,
                            'orderDetails' => $orderDetails,
                            'page' => $page + 1,
                            'pages' => count($groupOfOrderDetails),
                        ]
                    )
                )
            );
        }

        if (is_file(sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename))) {
            unlink(sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename));
        }

        $pdfMerger->merge('file', sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename));

        return sprintf('pdf/%s.pdf', $filename);
    }
}
