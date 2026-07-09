<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\LeaseModel;

class Rent extends BaseController
{
    protected $paymentModel;
    protected $leaseModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->leaseModel = new LeaseModel();
    }

    public function index()
    {
        $session = session();
        $role = $session->get('role');
        $userId = $session->get('id');

        if ($role === 'admin' || $role === 'owner') {
            $data['payments'] = $this->paymentModel->getPaymentsWithDetails();
            $data['leases'] = $this->leaseModel->getLeasesWithDetails();
            // Filter active leases
            $data['active_leases'] = array_filter($data['leases'], function($l) {
                return $l['status'] === 'active';
            });
        } else {
            $data['payments'] = $this->paymentModel->getPaymentsByUserId($userId);
            $data['active_leases'] = [];
        }

        return view('rent/index', $data);
    }

    public function store()
    {
        $rules = [
            'lease_id'       => 'required|integer',
            'amount'         => 'required|numeric',
            'payment_date'   => 'required|valid_date',
            'payment_method' => 'required|in_list[Cash,UPI,Bank Transfer,Online Payment]',
            'status'         => 'required|in_list[Paid,Pending,Overdue]',
            'notes'          => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $receiptNumber = 'REC-' . date('Y') . '-' . mt_rand(100000, 999999);
        while ($this->paymentModel->where('receipt_number', $receiptNumber)->first()) {
            $receiptNumber = 'REC-' . date('Y') . '-' . mt_rand(100000, 999999);
        }

        $data = [
            'lease_id'       => $this->request->getPost('lease_id'),
            'amount'         => $this->request->getPost('amount'),
            'payment_date'   => $this->request->getPost('payment_date'),
            'payment_method' => $this->request->getPost('payment_method'),
            'status'         => $this->request->getPost('status'),
            'receipt_number' => $receiptNumber,
            'notes'          => $this->request->getPost('notes'),
        ];

        $this->paymentModel->insert($data);

        return redirect()->to('/rent')->with('success', 'Rent payment recorded successfully! Receipt generated: ' . $receiptNumber);
    }

    public function receipt($id)
    {
        $payment = $this->paymentModel->getPaymentsWithDetails($id);
        if (!$payment) {
            return redirect()->to('/rent')->with('error', 'Payment record not found.');
        }

        $lease = $this->leaseModel->getLeasesWithDetails($payment['lease_id']);
        
        $data['payment'] = $payment;
        $data['lease'] = $lease;

        return view('rent/receipt', $data);
    }

    public function downloadPdf($id)
    {
        $payment = $this->paymentModel->getPaymentsWithDetails($id);
        if (!$payment) {
            return redirect()->to('/rent')->with('error', 'Payment record not found.');
        }

        $lease = $this->leaseModel->getLeasesWithDetails($payment['lease_id']);
        
        $data['payment'] = $payment;
        $data['lease'] = $lease;

        // Render clean HTML for Dompdf (using table layout instead of CSS flexbox)
        $html = view('rent/receipt_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream($payment['receipt_number'] . '_Receipt.pdf', ['Attachment' => 1]);
        exit;
    }

    public function delete($id)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/rent')->with('error', 'Payment record not found.');
        }

        $this->paymentModel->delete($id);
        return redirect()->to('/rent')->with('success', 'Rent payment record deleted.');
    }
}
