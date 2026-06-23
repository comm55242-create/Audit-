package com.melcomgroup.zahed.melcomshopaudit;

import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import java.util.List;

/* JADX INFO: loaded from: classes.dex */
public class ViewActivity extends AppCompatActivity {
    private ViewAdapter adapter;
    private RecyclerView recyclerView;

    @Override // android.support.v7.app.AppCompatActivity, android.support.v4.app.FragmentActivity, android.support.v4.app.BaseFragmentActivityGingerbread, android.app.Activity
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.data_view);
        this.recyclerView = (RecyclerView) findViewById(R.id.recyclerView);
        this.adapter = new ViewAdapter();
        this.recyclerView.setLayoutManager(new LinearLayoutManager(this));
        this.recyclerView.setAdapter(this.adapter);
        String userName = getIntent().getStringExtra("USERNAME");
        String rackNumber = getIntent().getStringExtra("RACK_NUMBER");
        new BackgroundWorker_view().execute(userName, rackNumber);
    }

    class BackgroundWorker_view extends AsyncTask<String, String, String> {
        BackgroundWorker_view() {
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
            if (Result == null) {
                android.widget.Toast.makeText(ViewActivity.this, "Device is not connected to the server.", 1).show();
                return;
            }
            Log.d("RESULT view", Result);
            try {
                List<Input_values_Vview> Result_list = (List) new Gson().fromJson(Result, new TypeToken<List<Input_values_Vview>>() { // from class: com.melcomgroup.zahed.melcomshopaudit.ViewActivity.BackgroundWorker_view.1
                }.getType());
                if (Result_list == null) {
                    throw new Exception("Null response list");
                }
                ViewActivity.this.adapter.setData(Result_list);
            } catch (Exception e) {
                android.widget.Toast.makeText(ViewActivity.this, "Server error or no data found.", 0).show();
            }
        }
    }
}
