<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentProductDetailController extends Controller
{
    public function store(Request $request, $id)
    {
        DB::beginTransaction();
    
        try {
            // Validate request data
            $validated = $request->validate([
                'purchase_amount' => 'required|numeric',
                'shipping_cost_inr' => 'required|numeric',
                'conversion_rate' => 'required|numeric',
                'shipping_cost_bdt' => 'required|numeric',
            ]);
    
            // Step 1: Add purchase_amount and shipping_cost_inr
            $intermediateValue = $validated['purchase_amount'] + $validated['shipping_cost_inr'];
    
            // Step 2: Multiply the intermediate value by conversion_rate
            $convertedValue = $intermediateValue * $validated['conversion_rate'];
    
            // Step 3: Add shipping_cost_bdt to the converted value
            $totalCogs = $convertedValue + $validated['shipping_cost_bdt'];
    
            // Find the shipment record
            $shipment = Shipment::findOrFail($id);
    
            // Use `updateOrCreate` to save or update shipment details
            $shipment->details()->updateOrCreate(
                ['shipment_id' => $id],
                [
                    'purchase_amount' => $validated['purchase_amount'], // Using hidden input field value
                    'shipping_cost_inr' => $validated['shipping_cost_inr'],
                    'conversion_rate' => $validated['conversion_rate'],
                    'shipping_cost_bdt' => $validated['shipping_cost_bdt'],
                    'total_cogs' => $totalCogs, // Save the calculated total_cogs
                ]
            );
    
            // Commit transaction
            DB::commit();
    
            flash(translate('Shipment details updated successfully.'))->success();
            return redirect()->back();
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
    
            // Log error details
            Log::error('Error saving ShipmentProductDetails:', ['error' => $e->getMessage()]);
    
            // Return error message
            return redirect()->back()->withErrors('Error saving data: ' . $e->getMessage());
        }
    }
    
    
}
