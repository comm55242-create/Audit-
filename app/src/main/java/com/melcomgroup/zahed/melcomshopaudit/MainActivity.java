package com.melcomgroup.zahed.melcomshopaudit;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.net.wifi.WifiInfo;
import android.net.wifi.WifiManager;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v7.app.AppCompatActivity;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import java.util.List;
import org.json.JSONException;

/* JADX INFO: loaded from: classes.dex */
public class MainActivity extends AppCompatActivity {
    TextView Username;
    TextView Zone_name;
    public TextView barcode_d;
    public TextView date_d;
    public TextView dept_d;
    public TextView ip_d;
    EditText item_code;
    public TextView item_code_d;
    public EditText item_code_scan;
    public TextView item_name_d;
    public TextView price_d;
    EditText qty;
    Button save;
    Button scan;
    public TextView shop_code_d;
    public TextView stock_d;
    String username;
    String zone1;

    @Override // android.support.v7.app.AppCompatActivity, android.support.v4.app.FragmentActivity, android.support.v4.app.BaseFragmentActivityGingerbread, android.app.Activity
    @RequiresApi(api = 24)
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        this.item_code_scan = (EditText) findViewById(R.id.editText_item);
        this.qty = (EditText) findViewById(R.id.editText_qty);

        // Automatically trigger SCAN when the hardware scanner presses Enter in the item code field
        this.item_code_scan.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, android.view.KeyEvent event) {
                if (actionId == android.view.inputmethod.EditorInfo.IME_ACTION_DONE || 
                    actionId == android.view.inputmethod.EditorInfo.IME_ACTION_NEXT || 
                    (event != null && event.getKeyCode() == android.view.KeyEvent.KEYCODE_ENTER && event.getAction() == android.view.KeyEvent.ACTION_DOWN)) {
                    
                    MainActivity.this.Onscan(null);
                    return true;
                }
                return false;
            }
        });
        this.item_code_d = (TextView) findViewById(R.id.textView_itemcode);
        this.item_name_d = (TextView) findViewById(R.id.textView_name);
        this.barcode_d = (TextView) findViewById(R.id.textView_barcode);
        this.price_d = (TextView) findViewById(R.id.textView_price);
        this.dept_d = (TextView) findViewById(R.id.textView_dept);
        this.shop_code_d = (TextView) findViewById(R.id.textView_shop);
        this.ip_d = (TextView) findViewById(R.id.textView_ip);
        this.Username = (TextView) findViewById(R.id.textView_username);
        this.Zone_name = (TextView) findViewById(R.id.textView_zone);
        this.username = getIntent().getStringExtra("USERNAME");
        this.zone1 = getIntent().getStringExtra("ZONE");
        this.scan = (Button) findViewById(R.id.button_scan);
        this.save = (Button) findViewById(R.id.button2);
        this.qty.setEnabled(false);
        this.qty.setText("");
        this.save.setEnabled(false);
        this.item_code_scan.setText("");
        this.item_code_scan.requestFocus();
        WifiManager wifiMan = (WifiManager) getSystemService("wifi");
        WifiInfo wifiInf = wifiMan.getConnectionInfo();
        int ipAddress = wifiInf.getIpAddress();
        String ip = String.format("%d.%d.%d.%d", Integer.valueOf(ipAddress & 255), Integer.valueOf((ipAddress >> 8) & 255), Integer.valueOf((ipAddress >> 16) & 255), Integer.valueOf((ipAddress >> 24) & 255));
        this.ip_d.setText(ip.toString());
        this.Username.setText(this.username.toString());
        this.Zone_name.setText(this.zone1.toString());
        this.qty.setText("");
        this.item_code_d.setText("");
        this.price_d.setText("");
        this.item_name_d.setText("");
        this.shop_code_d.setText("");
        this.barcode_d.setText("");
        this.item_code_d.setText("");
        this.dept_d.setText("");
        this.save.setEnabled(true);
        this.qty.setEnabled(true);
    }

    public void Onscan(View View) {
        String scannedCode = this.item_code_scan.getText().toString().trim();
        if (TextUtils.isEmpty(scannedCode)) {
            Toast.makeText(this, "Please Scan Item", 1).show();
            return;
        }
        // Force the text field to show the trimmed code
        this.item_code_scan.setText(scannedCode);
        
        this.qty.setText("");
        this.item_code_d.setText("");
        this.price_d.setText("");
        this.item_name_d.setText("");
        this.shop_code_d.setText("");
        this.barcode_d.setText("");
        this.item_code_d.setText("");
        this.dept_d.setText("");
        this.save.setEnabled(true);
        this.qty.setEnabled(true);
        Log.d("RESULT", "hi");
        this.qty.requestFocus();
        try {
            BackgroundWorker backgroundWorker = new BackgroundWorker(this);
            backgroundWorker.execute(scannedCode);
        } catch (Exception e) {
            Toast.makeText(this, "Connection Problem, Please check WiFi connection", 1).show();
        }
        InputMethodManager imm = (InputMethodManager) getSystemService("input_method");
        imm.toggleSoftInput(2, 0);
    }

    public void Onsave(View View) {
        if (TextUtils.isEmpty(this.qty.getText().toString())) {
            Toast.makeText(this, "Please check qty", 1).show();
            return;
        }
        int length = this.qty.length();
        String.valueOf(length);
        Log.d("RESULT", "hi");
        try {
            BackgroundWorker_save backgroundWorker = new BackgroundWorker_save(this);
            backgroundWorker.execute(this.shop_code_d.getText().toString(), this.item_code_d.getText().toString(), this.qty.getText().toString(), this.Username.getText().toString(), this.ip_d.getText().toString(), this.Zone_name.getText().toString());
        } catch (Exception e) {
            Toast.makeText(this, "Connection Problem, Please check WiFi connection", 1).show();
        }
        this.item_code_d.requestFocus();
        this.save.setEnabled(false);
        this.qty.setEnabled(false);
        this.qty.setText("");
        this.qty.setText("");
        this.item_code_d.setText("");
        this.price_d.setText("");
        this.item_name_d.setText("");
        this.item_code_scan.setText("");
        this.shop_code_d.setText("");
        this.barcode_d.setText("");
        this.item_code_d.setText("");
        this.dept_d.setText("");
        this.item_code_scan.requestFocus();
    }

    public void Onexit(View View) {
        finish();
        System.exit(0);
    }

    public void Onclear(View View) {
        this.item_code_scan.setText("");
        this.item_code_scan.requestFocus();
        this.qty.setEnabled(false);
    }

    public void Onview(View View) {
        Intent viewIntent = new Intent(this, (Class<?>) ViewActivity.class);
        viewIntent.putExtra("USERNAME", this.Username.getText().toString());
        viewIntent.putExtra("RACK_NUMBER", this.Zone_name.getText().toString());
        startActivity(viewIntent);
    }

    public void Onback(View View) {
        Intent i1 = new Intent(this, (Class<?>) LoginActivity.class);
        i1.addFlags(335577088);
        startActivity(i1);
    }

    class BackgroundWorker extends AsyncTask<String, String, String> {
        private Context contex;
        private Context contex1;

        BackgroundWorker(Context ctx) throws JSONException {
            this.contex = ctx;
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public String doInBackground(String... params) {
            String item_code = params[0];
            Uri.Builder builder = new Uri.Builder();
            builder.appendQueryParameter("item_code", item_code);
            String query = builder.build().getEncodedQuery();
            return network.post("http://172.16.33.9/shop_audit.php", query);
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public void onPostExecute(String Result) {
            Log.d("RESULT FINAL", Result);
            List<Input_values> Result_list = (List) new Gson().fromJson(Result, new TypeToken<List<Input_values>>() { // from class: com.melcomgroup.zahed.melcomshopaudit.MainActivity.BackgroundWorker.1
            }.getType());
            for (Input_values input_fileds : Result_list) {
                MainActivity.this.item_code_d.setText(input_fileds.getITEM_CODE());
                MainActivity.this.item_name_d.setText(input_fileds.getITEM_NAME());
                MainActivity.this.barcode_d.setText(input_fileds.getBARCODE());
                MainActivity.this.price_d.setText(input_fileds.getPRICE());
                MainActivity.this.dept_d.setText(input_fileds.getDEPT());
                MainActivity.this.shop_code_d.setText(input_fileds.getSHOP_CODE());
                
                String unit = input_fileds.getUNIT();
                String name = input_fileds.getITEM_NAME();
                boolean isKgs = false;
                if (unit != null && (unit.toUpperCase().contains("KGS") || unit.toUpperCase().contains("KG"))) {
                    isKgs = true;
                } else if (name != null && (name.toUpperCase().contains("KGS") || name.toUpperCase().contains("KG ") || name.toUpperCase().endsWith("KG"))) {
                    isKgs = true;
                }
                
                if (isKgs) {
                    MainActivity.this.qty.setInputType(android.text.InputType.TYPE_CLASS_NUMBER | android.text.InputType.TYPE_NUMBER_FLAG_DECIMAL | android.text.InputType.TYPE_NUMBER_FLAG_SIGNED);
                } else {
                    MainActivity.this.qty.setInputType(android.text.InputType.TYPE_CLASS_NUMBER | android.text.InputType.TYPE_NUMBER_FLAG_SIGNED);
                }
            }
            Integer n = Integer.valueOf(MainActivity.this.item_code_d.getText().length());
            if (n.intValue() <= 2) {
                Toast.makeText(MainActivity.this, "Item not found please check code again", 1).show();
                MainActivity.this.qty.setText("");
                MainActivity.this.qty.setEnabled(false);
                MainActivity.this.save.setEnabled(false);
            }
        }
    }

    class BackgroundWorker_save extends AsyncTask<String, String, String> {
        private Context contex;
        private Context contex1;
        private Context contex2;
        private Context contex3;

        BackgroundWorker_save(Context ctx) throws JSONException {
            this.contex = ctx;
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public String doInBackground(String... params) {
            String shop_code = params[0];
            String item_code = params[1];
            String qty = params[2];
            String user_name = params[3];
            String ip = params[4];
            String rack = params[5];
            Uri.Builder builder = new Uri.Builder();
            builder.appendQueryParameter("shop_code", shop_code);
            builder.appendQueryParameter("item_code", item_code);
            builder.appendQueryParameter("qty", qty);
            builder.appendQueryParameter("user", user_name);
            builder.appendQueryParameter("ip", ip);
            builder.appendQueryParameter("user", user_name);
            builder.appendQueryParameter("rack", rack);
            String query = builder.build().getEncodedQuery();
            return network.post("http://172.16.33.9/upload_audit_shop.php", query);
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public void onPostExecute(String Result) {
            Log.d("RESULT FINAL", Result);
        }
    }

    class BackgroundWorker_view extends AsyncTask<String, String, String> {
        private Context contex;
        private Context contex1;

        BackgroundWorker_view(Context ctx) throws JSONException {
            this.contex = ctx;
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public String doInBackground(String... params) {
            String username = params[0];
            String rack_num = params[1];
            Uri.Builder builder = new Uri.Builder();
            builder.appendQueryParameter("username", username);
            builder.appendQueryParameter("rack_num", rack_num);
            String query = builder.build().getEncodedQuery();
            return network.post("http://172.16.33.9/shop_report.php", query);
        }

        /* JADX INFO: Access modifiers changed from: protected */
        @Override // android.os.AsyncTask
        public void onPostExecute(String Result) {
            Log.d("RESULT view", Result);
            List<Input_values> Result_list = (List) new Gson().fromJson(Result, new TypeToken<List<Input_values>>() { // from class: com.melcomgroup.zahed.melcomshopaudit.MainActivity.BackgroundWorker_view.1
            }.getType());
            for (Input_values input_fileds : Result_list) {
                MainActivity.this.item_code_d.setText(input_fileds.getITEM_CODE());
                MainActivity.this.item_name_d.setText(input_fileds.getITEM_NAME());
                MainActivity.this.barcode_d.setText(input_fileds.getBARCODE());
                MainActivity.this.price_d.setText(input_fileds.getPRICE());
                MainActivity.this.dept_d.setText(input_fileds.getDEPT());
                MainActivity.this.shop_code_d.setText(input_fileds.getSHOP_CODE());
            }
            Integer n = Integer.valueOf(MainActivity.this.item_code_d.getText().length());
            if (n.intValue() <= 2) {
                Toast.makeText(MainActivity.this, "Item not found please check code again", 1).show();
                MainActivity.this.qty.setText("");
                MainActivity.this.qty.setEnabled(false);
                MainActivity.this.save.setEnabled(false);
            }
        }
    }
}
