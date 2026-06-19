import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

// Sesuaikan path import AppConstants ini dengan letak file kamu!
import '../../core/constants/api_constants.dart'; 
import '../../core/widgets/glass_background.dart';

class EditProdukPage extends StatefulWidget {
  final Map<String, dynamic> productData;

  const EditProdukPage({super.key, required this.productData});

  @override
  State<EditProdukPage> createState() => _EditProdukPageState();
}

class _EditProdukPageState extends State<EditProdukPage> {
  final _formKey = GlobalKey<FormState>();
  
  // Controllers untuk SEMUA kolom yang diminta PHP
  late TextEditingController _nameController;
  late TextEditingController _categoryIdController;
  late TextEditingController _skuController;
  late TextEditingController _priceController;
  late TextEditingController _stockController;
  late TextEditingController _descController;
  late TextEditingController _imageController;
  late TextEditingController _processorController;
  late TextEditingController _ramController;
  late TextEditingController _storageController;
  late TextEditingController _displayController;
  late TextEditingController _weightController;
  
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    // Mengisi data awal dari produk yang dipilih
    final p = widget.productData;
    _nameController = TextEditingController(text: p['name'] ?? '');
    _categoryIdController = TextEditingController(text: p['category_id']?.toString() ?? '1');
    _skuController = TextEditingController(text: p['sku'] ?? '');
    _priceController = TextEditingController(text: p['price']?.toString() ?? '0');
    _stockController = TextEditingController(text: p['stock']?.toString() ?? '0');
    _descController = TextEditingController(text: p['description'] ?? '');
    _imageController = TextEditingController(text: p['image_url'] ?? '');
    _processorController = TextEditingController(text: p['processor'] ?? '');
    _ramController = TextEditingController(text: p['ram'] ?? '');
    _storageController = TextEditingController(text: p['storage'] ?? '');
    _displayController = TextEditingController(text: p['display_size'] ?? '');
    _weightController = TextEditingController(text: p['weight'] ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _categoryIdController.dispose();
    _skuController.dispose();
    _priceController.dispose();
    _stockController.dispose();
    _descController.dispose();
    _imageController.dispose();
    _processorController.dispose();
    _ramController.dispose();
    _storageController.dispose();
    _displayController.dispose();
    _weightController.dispose();
    super.dispose();
  }

  Future<void> _updateProduk() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final String updateUrl = '${AppConstants.baseUrl}/products/update_product.php';

    try {
      final response = await http.post(
        Uri.parse(updateUrl),
        headers: {'Content-Type': 'application/json'},
        // Mengirim SEMUA data sesuai file PHP kamu
        body: jsonEncode({
          'id': widget.productData['id'],
          'category_id': int.tryParse(_categoryIdController.text) ?? 1,
          'name': _nameController.text,
          'sku': _skuController.text,
          'description': _descController.text,
          'price': double.tryParse(_priceController.text) ?? 0.0,
          'stock': int.tryParse(_stockController.text) ?? 0,
          'image_url': _imageController.text,
          'processor': _processorController.text,
          'ram': _ramController.text,
          'storage': _storageController.text,
          'display_size': _displayController.text,
          'weight': _weightController.text,
        }),
      );

      final data = jsonDecode(response.body);

      if (data['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Produk berhasil diupdate!'), backgroundColor: Colors.green),
          );
          Navigator.pop(context, true); // Kembali & refresh
        }
      } else {
        _showError(data['message'] ?? 'Gagal mengupdate produk');
      }
    } catch (e) {
      print("Error Update: $e");
      _showError('Terjadi kesalahan jaringan/server.');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showError(String message) {
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(message, style: const TextStyle(color: Colors.white)), backgroundColor: Colors.redAccent),
      );
    }
  }

  // Widget helper agar form tidak panjang-panjang kodenya
  Widget _buildTextField(TextEditingController controller, String label, {bool isNumber = false, int maxLines = 1}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextFormField(
        controller: controller,
        keyboardType: isNumber ? TextInputType.number : TextInputType.text,
        maxLines: maxLines,
        style: const TextStyle(color: Colors.white),
        decoration: InputDecoration(
          labelText: label,
          labelStyle: const TextStyle(color: Colors.white70),
          enabledBorder: OutlineInputBorder(
            borderSide: const BorderSide(color: Colors.white30),
            borderRadius: BorderRadius.circular(10),
          ),
          focusedBorder: OutlineInputBorder(
            borderSide: const BorderSide(color: Colors.blueAccent),
            borderRadius: BorderRadius.circular(10),
          ),
        ),
        validator: (value) => value!.isEmpty ? '$label wajib diisi' : null,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text('Edit Produk Lengkap', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: GlassBackground(
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20.0),
            child: Form(
              key: _formKey,
              child: ListView(
                physics: const BouncingScrollPhysics(),
                children: [
                  const SizedBox(height: 10),
                  const Text('Info Dasar', style: TextStyle(color: Colors.blueAccent, fontWeight: FontWeight.bold, fontSize: 16)),
                  const Divider(color: Colors.white30),
                  _buildTextField(_nameController, 'Nama Produk'),
                  _buildTextField(_categoryIdController, 'ID Kategori (Misal: 1)', isNumber: true),
                  _buildTextField(_skuController, 'SKU Produk'),
                  
                  const SizedBox(height: 10),
                  const Text('Harga & Stok', style: TextStyle(color: Colors.blueAccent, fontWeight: FontWeight.bold, fontSize: 16)),
                  const Divider(color: Colors.white30),
                  Row(
                    children: [
                      Expanded(child: _buildTextField(_priceController, 'Harga (Rp)', isNumber: true)),
                      const SizedBox(width: 10),
                      Expanded(child: _buildTextField(_stockController, 'Jumlah Stok', isNumber: true)),
                    ],
                  ),

                  const SizedBox(height: 10),
                  const Text('Spesifikasi Laptop', style: TextStyle(color: Colors.blueAccent, fontWeight: FontWeight.bold, fontSize: 16)),
                  const Divider(color: Colors.white30),
                  _buildTextField(_processorController, 'Processor (Misal: Intel Core i7)'),
                  Row(
                    children: [
                      Expanded(child: _buildTextField(_ramController, 'RAM (Misal: 16GB)')),
                      const SizedBox(width: 10),
                      Expanded(child: _buildTextField(_storageController, 'Storage (Misal: 512GB SSD)')),
                    ],
                  ),
                  Row(
                    children: [
                      Expanded(child: _buildTextField(_displayController, 'Ukuran Layar (Misal: 15.6")')),
                      const SizedBox(width: 10),
                      Expanded(child: _buildTextField(_weightController, 'Berat (Misal: 1.8 kg)')),
                    ],
                  ),

                  const SizedBox(height: 10),
                  const Text('Lainnya', style: TextStyle(color: Colors.blueAccent, fontWeight: FontWeight.bold, fontSize: 16)),
                  const Divider(color: Colors.white30),
                  _buildTextField(_imageController, 'URL Gambar'),
                  _buildTextField(_descController, 'Deskripsi Produk', maxLines: 3),
                  
                  const SizedBox(height: 20),
                  SizedBox(
                    height: 50,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _updateProduk,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blueAccent,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      child: _isLoading
                          ? const CircularProgressIndicator(color: Colors.white)
                          : const Text('SIMPAN PERUBAHAN', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                    ),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
